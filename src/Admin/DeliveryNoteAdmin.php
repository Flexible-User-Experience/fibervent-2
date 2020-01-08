<?php

namespace App\Admin;

use App\Entity\User;
use App\Entity\Vehicle;
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
        $collection->remove('batch');
//            ->add('pdf', $this->getRouterIdParameter().'/pdf')
//            ->add('email', $this->getRouterIdParameter().'/email');
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('admin.common.general', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'workOrder',
                null,
                array(
                    'label' => 'admin.workorder.title',
                    'required' => true,
                )
            )
            ->add(
                'date',
                DatePickerType::class,
                array(
                    'label' => 'admin.deliverynote.date',
                    'format' => 'd/M/y',
                )
            )
            ->add(
                'repairWindmillSections',
                ChoiceType::class,
                array(
                    'label' => 'admin.deliverynote.repair_windmill_sections',
                    'choices' => RepairWindmillSectionEnum::getEnumArray(),
                    'multiple' => true,
                    'expanded' => false,
                    'required' => true,
                )
            )
            ->end()
            ->with('admin.deliverynote.team', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'teamLeader',
                EntityType::class,
                array(
                    'label' => 'admin.deliverynote.team_leader',
                    'class' => User::class,
                    'required' => true,
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
                'repairAccessTypes',
                ChoiceType::class,
                array(
                    'label' => 'admin.deliverynote.repair_access_types',
                    'choices' => RepairAccessTypeEnum::getEnumArray(),
                    'multiple' => true,
                    'expanded' => false,
                    'required' => true,
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
            ->with('admin.workordertask.title', $this->getFormMdSuccessBoxArray(12))
            ->add(
                'workOrderTasks',
                ModelType::class,
                array(
                    'label' => 'admin.workordertask.title',
                    'multiple' => true,
                    'expanded' => false,
                    'required' => false,
                    'btn_add' => false,
                )
            )
            ->end()
            ->with('admin.nonstandardusedmaterial.title', $this->getFormMdSuccessBoxArray(6))
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
            ->with('admin.deliverynote.observations', $this->getFormMdSuccessBoxArray(6))
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
            ->end()
        ;
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
                )
            )
            ->add(
                'repairWindmillSections',
                null,
                array(
                    'label' => 'admin.deliverynote.repair_windmill_sections_short',
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
                'teamLeader',
                null,
                array(
                    'label' => 'admin.deliverynote.team_leader',
                ),
                EntityType::class,
                array(
                    'class' => User::class,
                    'query_builder' => $this->ur->findAllSortedByNameQB(),
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
                    'query_builder' => $this->ur->findAllSortedByNameQB(),
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
                    'query_builder' => $this->ur->findAllSortedByNameQB(),
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
                    'query_builder' => $this->ur->findAllSortedByNameQB(),
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
                    'query_builder' => $this->ur->findAllSortedByNameQB(),
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
                'repairWindmillSections',
                null,
                array(
                    'label' => 'admin.deliverynote.repair_windmill_sections_short',
                    'header_class' => 'text-center',
                    'row_align' => 'center',
                    'template' => 'Admin/Cells/list__cell_repair_windmill_sections.html.twig',
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
                'teamTechnician2',
                null,
                array(
                    'label' => 'admin.deliverynote.team_technician_2',
                    'sortable' => true,
                    'sort_field_mapping' => array('fieldName' => 'firstname'),
                    'sort_parent_association_mappings' => array(array('fieldName' => 'teamTechnician2')),
                )
            )
            ->add(
                'vehicle',
                null,
                array(
                    'label' => 'admin.vehicle.title',
                    'sortable' => true,
                    'sort_field_mapping' => array('fieldName' => 'name'),
                    'sort_parent_association_mappings' => array(array('fieldName' => 'vehicle')),
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
                        // 'excel' => array('template' => 'Admin/Buttons/list__action_excel_button.html.twig'),
                        // 'pdf' => array('template' => 'Admin/Buttons/list__action_pdf_windfarm_button.html.twig'),
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
                'repairWindmillSections',
                null,
                array(
                    'label' => 'admin.deliverynote.repair_windmill_sections',
                    'template' => 'Admin/Cells/list__cell_repair_windmill_sections.html.twig',
                )
            )
            ->end()
            ->with('admin.deliverynote.team', $this->getFormMdSuccessBoxArray(4))
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
                'repairAccessTypes',
                null,
                array(
                    'label' => 'admin.workorder.repair_access_types',
                    'template' => 'Admin/Cells/list__cell_repair_access_type.html.twig',
                )
            )
            ->end()
            ->with('admin.deliverynotetimeregister.title', $this->getFormMdSuccessBoxArray(6))
            ->add(
                'timeRegisters',
                null,
                array(
                    'label' => 'admin.deliverynote.repair_windmill_sections',
                    'template' => 'Admin/Cells/list__time_registers.html.twig',
                )
            )
            ->end()
            ->with('admin.nonstandardusedmaterial.title', $this->getFormMdSuccessBoxArray(6))
            ->add(
                'nonStandardUsedMaterials',
                null,
                array(
                    'label' => 'admin.nonstandardusedmaterial.title',
                    'template' => 'Admin/Cells/list__non_standard_used_materials.html.twig',
                )
            )
            ->end()
            ->with('admin.deliverynote.observations', $this->getFormMdSuccessBoxArray(8))
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
}
