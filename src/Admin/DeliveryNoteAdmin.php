<?php

namespace App\Admin;

use App\Entity\Customer;
use App\Entity\DeliveryNote;
use App\Entity\DeliveryNoteTimeRegister;
use App\Entity\User;
use App\Entity\Vehicle;
use App\Entity\Windfarm;
use App\Entity\Windmill;
use App\Entity\WorkOrder;
use App\Enum\BladeEnum;
use App\Enum\RepairAccessTypeEnum;
use App\Enum\RepairWindmillSectionEnum;
use App\Enum\UserRolesEnum;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\CollectionType;
use Sonata\Form\Type\DatePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * Class DeliveryNoteAdmin
 *
 * @category Admin
 */
class DeliveryNoteAdmin extends AbstractBaseAdmin
{
    protected $classnameLabel = 'admin.deliverynote.title';
    protected $baseRoutePattern = 'workorders/deliverynote';
    protected $datagridValues = array(
        '_sort_by' => 'date',
        '_sort_order' => 'desc',
    );

    /**
     * Configure route collection.
     *
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('batch')
            ->add('getWindfarmsFromWorkOrderId', $this->getRouterIdParameter().'/get-windfarms-from-work-order-id')
            ->add('pdf', $this->getRouterIdParameter().'/pdf')
        ;
    }

    /**
     * @param string $context
     *
     * @return QueryBuilder
     */
    public function createQuery($context = 'list')
    {
        /** @var User $user */
        $user = $this->tss->getToken()->getUser();
        /** @var QueryBuilder $query */
        $query = parent::createQuery($context);
        if ($user->hasRole(UserRolesEnum::ROLE_OPERATOR) || $user->hasRole(UserRolesEnum::ROLE_TECHNICIAN)) {
            /** @var string $ra */
            $ra = $query->getRootAliases()[0];
            $query
                ->andWhere($ra.'.teamLeader = :teamLeader OR '.$ra.'.teamTechnician1 = :teamTechnician1 OR '.$ra.'.teamTechnician2 = :teamTechnician2 OR '.$ra.'.teamTechnician3 = :teamTechnician3 OR '.$ra.'.teamTechnician4 = :teamTechnician4')
                ->setParameter('teamLeader', $user)
                ->setParameter('teamTechnician1', $user)
                ->setParameter('teamTechnician2', $user)
                ->setParameter('teamTechnician3', $user)
                ->setParameter('teamTechnician4', $user)
            ;
        }

        return $query;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $isNewRecord = $this->id($this->getSubject()) ? false : true;
        /** @var WorkOrder[]|array $availableWorkOrders */
        $availableWorkOrders = $this->wor->findAvailableSortedByProjectNumber();
        /** @var Windfarm[]|array $availableWindfarms */
        $availableWindfarms = $this->wfr->findMultipleRelatedWithAWorkOrdersArraySortedByName($availableWorkOrders);
        $formMapper
            ->with('admin.common.general', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'date',
                DatePickerType::class,
                array(
                    'label' => 'admin.deliverynote.date',
                    'format' => 'd/M/y',
                )
            )
            ->add(
                'workOrder',
                EntityType::class,
                array(
                    'label' => 'admin.workorder.title',
                    'class' => WorkOrder::class,
                    'query_builder' => $this->wor->findAvailableSortedByProjectNumberQB(),
                    'required' => true,
                )
            )
            ->add(
                'windfarm',
                EntityType::class,
                array(
                    'label' => 'admin.windfarm.title',
                    'class' => Windfarm::class,
                    'query_builder' => $this->wfr->findMultipleRelatedWithAWorkOrdersArraySortedByNameQB($availableWorkOrders),
                )
            )
            ->end()
            ->with('admin.deliverynote.pdf.windfarm_data', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'windmill',
                EntityType::class,
                array(
                    'label' => 'admin.windmill.title',
                    'class' => Windmill::class,
                    'query_builder' => $this->wmr->findMultipleByWindfarmsArrayQB($availableWindfarms),
                )
            )
            ->add(
                'repairWindmillSections',
                ChoiceType::class,
                array(
                    'label' => 'admin.deliverynote.pdf.work_in',
                    'choices' => RepairWindmillSectionEnum::getEnumArray(),
                    'multiple' => true,
                    'expanded' => true,
                    'required' => false,
                )
            )
            ->add(
                'blades',
                ChoiceType::class,
                array(
                    'label' => 'admin.deliverynote.pdf.blade_number',
                    'choices' => BladeEnum::getEnumArray(),
                    'multiple' => true,
                    'expanded' => true,
                    'required' => false,
                )
            )
            ->end()
            ->with('admin.deliverynote.pdf.business_data', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'teamLeader',
                EntityType::class,
                array(
                    'label' => 'admin.deliverynote.team_leader',
                    'class' => User::class,
                    'query_builder' => $this->ur->findAllTechniciansSortedByNameQB(),
                    'choice_label' => 'getFullnameCanonical',
                    'required' => true,
                )
            )
            ->add(
                'teamTechnician1',
                EntityType::class,
                array(
                    'label' => 'admin.deliverynote.team_technician_1',
                    'class' => User::class,
                    'query_builder' => $this->ur->findAllTechniciansSortedByNameQB(),
                    'choice_label' => 'getFullnameCanonical',
                    'required' => false,
                )
            )
            ->add(
                'teamTechnician2',
                EntityType::class,
                array(
                    'label' => 'admin.deliverynote.team_technician_2',
                    'class' => User::class,
                    'query_builder' => $this->ur->findAllTechniciansSortedByNameQB(),
                    'choice_label' => 'getFullnameCanonical',
                    'required' => false,
                )
            )
            ->add(
                'teamTechnician3',
                EntityType::class,
                array(
                    'label' => 'admin.deliverynote.team_technician_3',
                    'class' => User::class,
                    'query_builder' => $this->ur->findAllTechniciansSortedByNameQB(),
                    'choice_label' => 'getFullnameCanonical',
                    'required' => false,
                )
            )
            ->add(
                'teamTechnician4',
                EntityType::class,
                array(
                    'label' => 'admin.deliverynote.team_technician_4',
                    'class' => User::class,
                    'query_builder' => $this->ur->findAllTechniciansSortedByNameQB(),
                    'choice_label' => 'getFullnameCanonical',
                    'required' => false,
                )
            )
            ->add(
                'vehicle',
                EntityType::class,
                array(
                    'label' => 'admin.vehicle.title',
                    'class' => Vehicle::class,
                    'query_builder' => $this->vr->findEnabledSortedByNameQB(),
                )
            )
            ->end()
            ->with('admin.deliverynote.repair_access_types', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'repairAccessTypes',
                ChoiceType::class,
                array(
                    'label' => 'admin.deliverynote.repair_access_types',
                    'choices' => RepairAccessTypeEnum::getEnumArray(),
                    'multiple' => true,
                    'expanded' => true,
                    'required' => false,
                )
            )
            ->add(
                'craneCompany',
                null,
                array(
                    'label' => 'admin.deliverynote.crane_company',
                )
            )
            ->add(
                'craneDriver',
                null,
                array(
                    'label' => 'admin.deliverynote.crane_driver',
                )
            )
            ->end()
        ;
        if (!$isNewRecord) {
            /** @var DeliveryNote $deliveryNote */
            $deliveryNote = $this->getSubject();
            /** @var WorkOrder $workOrder */
            $workOrder = $deliveryNote->getWorkOrder();
            /** @var Windmill $windmill */
            $windmill = $deliveryNote->getWindmill();
            $formMapper
                ->with('admin.workordertask.title', $this->getFormMdSuccessBoxArray(8))
                ->add(
                    'workOrderTasks',
                    ModelType::class,
                    array(
                        'label' => 'admin.workordertask.title',
                        'multiple' => true,
                        'expanded' => true,
                        'required' => false,
                        'by_reference' => false,
                        'property' => 'getLongDescriptionForEmbedForm',
                        'query' => $this->wotr->findItemsByWorkOrderAndWindmillSortedByIdQB($workOrder, $windmill),
                    )
                )
                ->end()
                ->with('admin.deliverynotetimeregister.title', $this->getFormMdSuccessBoxArray(12))
                ->add(
                    'timeRegisters',
                    CollectionType::class,
                    array(
                        'label' => ' ',
                        'required' => false,
                        'btn_add' => true,
                        'error_bubbling' => true,
                        'type_options' => array(
                            'delete' => true,
                        ),
                    ),
                    array(
                        'edit' => 'inline',
                        'inline' => 'table',
                    )
                )
                ->end()
                ->with('admin.nonstandardusedmaterial.title', $this->getFormMdSuccessBoxArray(8))
                ->add(
                    'nonStandardUsedMaterials',
                    CollectionType::class,
                    array(
                        'label' => ' ',
                        'required' => false,
                        'btn_add' => true,
                        'error_bubbling' => true,
                        'type_options' => array(
                            'delete' => true,
                        ),
                    ),
                    array(
                        'edit' => 'inline',
                        'inline' => 'table',
                    )
                )
                ->end()
                ->with('admin.deliverynote.observations', $this->getFormMdSuccessBoxArray(4))
                ->add(
                    'observations',
                    TextareaType::class,
                    array(
                        'label' => 'admin.deliverynote.observations',
                        'required' => false,
                        'attr' => array(
                            'rows' => 6,
                        ),
                    )
                )
            ;
        }
        $formMapper->end();
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add(
                'id',
                null,
                array(
                    'label' => 'admin.deliverynote.id',
                )
            )
            ->add(
                'workOrder',
                null,
                array(
                    'label' => 'admin.workorder.title',
                ),
                EntityType::class,
                array(
                    'class' => WorkOrder::class,
                    'query_builder' => $this->wor->findAllSortedByProjectNumberQB(),
                )
            )
            ->add(
                'date',
                'doctrine_orm_date',
                array(
                    'label' => 'admin.deliverynote.date',
                    'field_type' => DatePickerType::class,
                    'format' => 'd/m/Y',
                ),
                null,
                array(
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',
                )
            )
            ->add(
                'workOrder.customer',
                null,
                array(
                    'label' => 'admin.customer.title',
                ),
                EntityType::class,
                array(
                    'class' => Customer::class,
                    'query_builder' => $this->cr->findAllSortedByNameQB(),
                )
            )
            ->add(
                'windfarm',
                null,
                array(
                    'label' => 'admin.windfarm.title',
                ),
                EntityType::class,
                array(
                    'class' => Windfarm::class,
                    'query_builder' => $this->wfr->findAllSortedByNameQB(),
                )
            )
            ->add(
                'windfarm.city',
                null,
                array(
                    'label' => 'admin.deliverynote.pdf.city',
                )
            )
            ->add(
                'windmill',
                null,
                array(
                    'label' => 'admin.windmill.title',
                ),
                EntityType::class,
                array(
                    'class' => Windmill::class,
                    'query_builder' => $this->wmr->findEnabledSortedByCustomerWindfarmAndWindmillCodeQB(),
                )
            )
            ->add(
                'repairWindmillSections',
                null,
                array(
                    'label' => 'admin.deliverynote.pdf.work_in',
                ),
                ChoiceType::class,
                array(
                    'choices' => RepairWindmillSectionEnum::getDatagridFilterEnumArray(),
                    'multiple' => false,
                    'expanded' => false,
                    'required' => false,
                )
            )
            ->add(
                'blades',
                null,
                array(
                    'label' => 'admin.deliverynote.pdf.blade_number',
                ),
                ChoiceType::class,
                array(
                    'choices' => BladeEnum::getLongTextEnumArray(),
                    'multiple' => false,
                    'expanded' => false,
                    'required' => false,
                )
            )
            ->add(
                'windmill.bladeType.model',
                null,
                array(
                    'label' => 'admin.deliverynote.pdf.serial_number',
                )
            )
            ->add(
                'teamLeader',
                null,
                array(
                    'label' => 'admin.deliverynote.team_leader',
                ),
                EntityType::class,
                array(
                    'class' => User::class,
                    'query_builder' => $this->ur->findAllTechniciansSortedByNameQB(),
                    'choice_label' => 'getFullnameCanonical',
                )
            )
            ->add(
                'teamTechnician1',
                null,
                array(
                    'label' => 'admin.deliverynote.team_technician_1',
                ),
                EntityType::class,
                array(
                    'class' => User::class,
                    'query_builder' => $this->ur->findAllTechniciansSortedByNameQB(),
                    'choice_label' => 'getFullnameCanonical',
                )
            )
            ->add(
                'teamTechnician2',
                null,
                array(
                    'label' => 'admin.deliverynote.team_technician_2',
                ),
                EntityType::class,
                array(
                    'class' => User::class,
                    'query_builder' => $this->ur->findAllTechniciansSortedByNameQB(),
                    'choice_label' => 'getFullnameCanonical',
                )
            )
            ->add(
                'teamTechnician3',
                null,
                array(
                    'label' => 'admin.deliverynote.team_technician_3',
                ),
                EntityType::class,
                array(
                    'class' => User::class,
                    'query_builder' => $this->ur->findAllTechniciansSortedByNameQB(),
                    'choice_label' => 'getFullnameCanonical',
                )
            )
            ->add(
                'teamTechnician4',
                null,
                array(
                    'label' => 'admin.deliverynote.team_technician_4',
                ),
                EntityType::class,
                array(
                    'class' => User::class,
                    'query_builder' => $this->ur->findAllTechniciansSortedByNameQB(),
                    'choice_label' => 'getFullnameCanonical',
                )
            )
            ->add(
                'vehicle',
                null,
                array(
                    'label' => 'admin.vehicle.title',
                ),
                EntityType::class,
                array(
                    'class' => Vehicle::class,
                    'query_builder' => $this->vr->findAllSortedByNameQB(),
                )
            )
            ->add(
                'repairAccessTypes',
                null,
                array(
                    'label' => 'admin.workorder.repair_access_types',
                ),
                ChoiceType::class,
                array(
                    'choices' => RepairAccessTypeEnum::getDatagridFilterEnumArray(),
                    'multiple' => false,
                    'expanded' => false,
                    'required' => false,
                )
            )
            ->add(
                'craneCompany',
                null,
                array(
                    'label' => 'admin.deliverynote.crane_company',
                )
            )
            ->add(
                'craneDriver',
                null,
                array(
                    'label' => 'admin.deliverynote.crane_driver',
                )
            )
            ->add(
                'observations',
                null,
                array(
                    'label' => 'admin.deliverynote.observations',
                )
            )
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add(
                'id',
                null,
                array(
                    'label' => 'admin.deliverynote.id',
                    'row_align' => 'right',
                    'header_class' => 'text-right',
                )
            )
            ->add(
                'workOrder',
                null,
                array(
                    'label' => 'admin.workorder.title',
                    'sortable' => true,
                    'sort_field_mapping' => array('fieldName' => 'projectNumber'),
                    'sort_parent_association_mappings' => array(array('fieldName' => 'workOrder')),
                    'row_align' => 'center',
                    'header_class' => 'text-center',
                )
            )
            ->add(
                'date',
                null,
                array(
                    'label' => 'admin.deliverynote.date',
                    'format' => 'd/m/Y',
                    'row_align' => 'center',
                    'header_class' => 'text-center',
                )
            )
            ->add(
                'workOrder.customer',
                null,
                array(
                    'label' => 'admin.customer.title',
                    'associated_property' => 'name',
                    'sortable' => true,
                    'sort_field_mapping' => array('fieldName' => 'name'),
                    'sort_parent_association_mappings' => array(
                        array('fieldName' => 'workOrder'),
                        array('fieldName' => 'customer'),
                    ),
                )
            )
            ->add(
                'windfarm',
                null,
                array(
                    'label' => 'admin.windfarm.title',
                    'sortable' => true,
                    'sort_field_mapping' => array('fieldName' => 'name'),
                    'sort_parent_association_mappings' => array(array('fieldName' => 'windfarm')),
                )
            )
            ->add(
                'windmill',
                null,
                array(
                    'label' => 'admin.windmill.title',
                    'associated_property' => 'code',
                    'sortable' => true,
                    'sort_field_mapping' => array('fieldName' => 'code'),
                    'sort_parent_association_mappings' => array(array('fieldName' => 'windmill')),
                )
            )
            ->add(
                'repairWindmillSections',
                null,
                array(
                    'label' => 'admin.deliverynote.pdf.work_in',
                    'header_class' => 'text-center',
                    'row_align' => 'center',
                    'template' => 'Admin/Cells/list__cell_repair_windmill_sections.html.twig',
                )
            )
            ->add(
                'blades',
                null,
                array(
                    'label' => 'admin.deliverynote.pdf.blade_number',
                    'header_class' => 'text-center',
                    'row_align' => 'center',
                    'template' => 'Admin/Cells/list__cell_blades.html.twig',
                )
            )
            ->add(
                'teamLeader',
                null,
                array(
                    'label' => 'admin.deliverynote.team_leader',
                    'sortable' => true,
                    'sort_field_mapping' => array('fieldName' => 'firstname'),
                    'sort_parent_association_mappings' => array(array('fieldName' => 'teamLeader')),
                )
            )
            ->add(
                'teamTechnician1',
                null,
                array(
                    'label' => 'admin.deliverynote.team_technician_1',
                    'sortable' => true,
                    'sort_field_mapping' => array('fieldName' => 'firstname'),
                    'sort_parent_association_mappings' => array(array('fieldName' => 'teamTechnician1')),
                )
            )
            ->add(
                'repairAccessTypes',
                null,
                array(
                    'label' => 'admin.workorder.repair_access_types',
                    'template' => 'Admin/Cells/list__cell_repair_access_type.html.twig',
                    'header_class' => 'text-center',
                    'row_align' => 'center',
                )
            )
            ->add(
                '_action',
                'actions',
                array(
                    'label' => 'admin.common.action',
                    'header_class' => 'text-right',
                    'row_align' => 'right',
                    'actions' => array(
                        'edit' => array('template' => 'Admin/Buttons/list__action_edit_button.html.twig'),
                        'show' => array('template' => 'Admin/Buttons/list__action_show_button.html.twig'),
                        'pdf' => array('template' => 'Admin/Buttons/list__action_pdf_windfarm_button.html.twig'),
                        'create_timesheet' => array('template' => 'Admin/Buttons/list__action_create_new_worker_timesheet_from_delivery_note_windfarm_button.html.twig'),
                        'delete' => array('template' => 'Admin/Buttons/list__action_super_admin_delete_button.html.twig'),
                    ),
                )
            )
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('admin.common.general', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'id',
                null,
                array(
                    'label' => 'admin.deliverynote.id',
                )
            )
            ->add(
                'workOrder',
                null,
                array(
                    'label' => 'admin.workorder.title',
                )
            )
            ->add(
                'date',
                null,
                array(
                    'label' => 'admin.deliverynote.date',
                    'format' => 'd/m/Y',
                )
            )
            ->add(
                'workOrder.customer',
                null,
                array(
                    'label' => 'admin.customer.title',
                )
            )
            ->end()
            ->with('admin.deliverynote.pdf.windfarm_data', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'windfarm',
                null,
                array(
                    'label' => 'admin.windfarm.title',
                )
            )
            ->add(
                'windfarm.city',
                null,
                array(
                    'label' => 'admin.customer.city',
                )
            )
            ->add(
                'windmill.code',
                null,
                array(
                    'label' => 'admin.windmill.title',
                )
            )
            ->add(
                'repairWindmillSections',
                null,
                array(
                    'label' => 'admin.deliverynote.pdf.work_in',
                    'template' => 'Admin/Cells/show__repair_windmill_sections.html.twig',
                )
            )
            ->add(
                'blades',
                null,
                array(
                    'label' => 'admin.deliverynote.pdf.blade_number',
                    'template' => 'Admin/Cells/show__blades.html.twig',
                )
            )
            ->add(
                'windmill.bladeType.model',
                null,
                array(
                    'label' => 'admin.deliverynote.pdf.serial_number',
                )
            )
            ->end()
            ->with('admin.deliverynote.pdf.business_data', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'teamLeader',
                null,
                array(
                    'label' => 'admin.deliverynote.team_leader',
                )
            )
            ->add(
                'teamTechnician1',
                null,
                array(
                    'label' => 'admin.deliverynote.team_technician_1',
                )
            )
            ->add(
                'teamTechnician2',
                null,
                array(
                    'label' => 'admin.deliverynote.team_technician_2',
                )
            )
            ->add(
                'teamTechnician3',
                null,
                array(
                    'label' => 'admin.deliverynote.team_technician_3',
                )
            )
            ->add(
                'teamTechnician4',
                null,
                array(
                    'label' => 'admin.deliverynote.team_technician_4',
                )
            )
            ->add(
                'vehicle',
                null,
                array(
                    'label' => 'admin.vehicle.title',
                )
            )
            ->end()
            ->with('admin.deliverynote.repair_access_types', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'repairAccessTypes',
                null,
                array(
                    'label' => 'admin.workorder.repair_access_types',
                    'template' => 'Admin/Cells/show__extends_repair_access_type.html.twig',
                )
            )
            ->add(
                'craneCompany',
                null,
                array(
                    'label' => 'admin.deliverynote.crane_company',
                )
            )
            ->add(
                'craneDriver',
                null,
                array(
                    'label' => 'admin.deliverynote.crane_driver',
                )
            )
            ->end()
            ->with('admin.workordertask.title', $this->getFormMdSuccessBoxArray(8))
            ->add(
                'workOrderTasks',
                null,
                array(
                    'label' => 'admin.workordertask.title',
                    'template' => 'Admin/Cells/show__delivery_note_work_order_tasks.html.twig',
                )
            )
            ->end()
            ->with('admin.deliverynotetimeregister.title', $this->getFormMdSuccessBoxArray(12))
            ->add(
                'timeRegisters',
                null,
                array(
                    'label' => 'admin.deliverynote.repair_windmill_sections',
                    'template' => 'Admin/Cells/show__time_registers.html.twig',
                )
            )
            ->end()
            ->with('admin.nonstandardusedmaterial.title', $this->getFormMdSuccessBoxArray(6))
            ->add(
                'nonStandardUsedMaterials',
                null,
                array(
                    'label' => 'admin.nonstandardusedmaterial.title',
                    'template' => 'Admin/Cells/show__non_standard_used_materials.html.twig',
                )
            )
            ->end()
            ->with('admin.deliverynote.observations', $this->getFormMdSuccessBoxArray(6))
            ->add(
                'observations',
                null,
                array(
                    'label' => 'admin.deliverynote.observations',
                )
            )
            ->end()
        ;
    }

    /**
     * @param object|DeliveryNote $object
     */
    function prePersist($object)
    {
        $this->commonUpdates($object);
    }

    /**
     * @param object|DeliveryNote $object
     */
    function preUpdate($object)
    {
        $this->commonUpdates($object);
    }

    /**
     * @param DeliveryNote $object
     */
    private function commonUpdates($object)
    {
        /** @var DeliveryNoteTimeRegister $dntr */
        foreach ($object->getTimeRegisters() as $dntr) {
            $dntr->setTotalHours($dntr->getDifferenceBetweenEndAndBeginHoursInDecimalHours());
        }
    }
}
