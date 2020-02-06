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
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $isNewRecord = $this->id($this->getSubject()) ? false : true;
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
            ->end()
            ->with('admin.deliverynote.pdf.customer_data', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'windfarm',
                EntityType::class,
                array(
                    'label' => 'admin.windfarm.title',
                    'class' => Windfarm::class,
                    'query_builder' => $this->wfr->findAllSortedByNameQB(),
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
                    'query_builder' => $this->wmr->findEnabledSortedByCustomerWindfarmAndWindmillCodeQB(),
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
            $formMapper
                ->with('admin.workordertask.title', $this->getFormMdSuccessBoxArray(4))
                ->add(
                    'workOrderTasks',
                    ModelType::class,
                    array(
                        'label' => 'admin.workordertask.title',
                        'multiple' => true,
                        'expanded' => true,
                        'required' => false,
                        'btn_add' => false,
                        'query' => $this->wotr->findItemsByWorkOrderSortedByIdQB($this->getSubject()->getWorkOrder()),
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
                'id',
                null,
                array(
                    'label' => 'admin.deliverynote.id',
                    'row_align' => 'center',
                    'header_class' => 'text-center',
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
                'workOrder.customer',
                null,
                array(
                    'label' => 'admin.customer.title',
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
                'date',
                null,
                array(
                    'label' => 'admin.deliverynote.date',
                    'format' => 'd/m/Y',
                )
            )
            ->add(
                'id',
                null,
                array(
                    'label' => 'admin.workorder.project_number_short',
                )
            )
            ->add(
                'workOrder',
                null,
                array(
                    'label' => 'admin.workorder.title',
                )
            )
            ->end()
            ->with('admin.deliverynote.pdf.customer_data', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'workOrder.customer',
                null,
                array(
                    'label' => 'admin.customer.title',
                )
            )
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
            ->end()
            ->with('admin.deliverynote.pdf.windfarm_data', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'windmill',
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
            // TODO insert here "Descripcion del trabajo realizado"
            ->with('admin.nonstandardusedmaterial.title', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'nonStandardUsedMaterials',
                null,
                array(
                    'label' => 'admin.nonstandardusedmaterial.title',
                    'template' => 'Admin/Cells/show__non_standard_used_materials.html.twig',
                )
            )
            ->end()
            ->with('admin.deliverynotetimeregister.title', $this->getFormMdSuccessBoxArray(8))
            ->add(
                'timeRegisters',
                null,
                array(
                    'label' => 'admin.deliverynote.repair_windmill_sections',
                    'template' => 'Admin/Cells/show__time_registers.html.twig',
                )
            )
            ->end()
            ->with('admin.deliverynote.observations', $this->getFormMdSuccessBoxArray(4))
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
