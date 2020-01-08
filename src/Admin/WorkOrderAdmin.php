<?php

namespace App\Admin;

use App\Entity\Audit;
use App\Entity\Customer;
use App\Entity\Windfarm;
use App\Enum\RepairAccessTypeEnum;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\CollectionType;
use Sonata\Form\Type\DatePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Class WorkOrderAdmin.
 *
 * @category Admin
 */
class WorkOrderAdmin extends AbstractBaseAdmin
{
    protected $classnameLabel = 'admin.workorder.title';
    protected $baseRoutePattern = 'workorders/workorder';
    protected $datagridValues = array(
        '_sort_by' => 'createdAt',
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
            ->add('getWindfarmsFromCustomerId', $this->getRouterIdParameter().'/get-windfarms-from-customer-id')
            ->add('getWindmillbladesFromWindmillId', $this->getRouterIdParameter().'/get-windmillblades-from-windmill-id')
            ->add('pdf', $this->getRouterIdParameter().'/pdf')
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        if ($this->id($this->getSubject())) { // is edit mode, disable on new subjects
            $formMapper
                ->with('admin.common.general', $this->getFormMdSuccessBoxArray(4))
                ->add(
                    'projectNumber',
                    null,
                    array(
                        'label' => 'admin.workorder.project_number',
                        'disabled' => true,
                    )
                )
                ->add(
                    'customer',
                    EntityType::class,
                    array(
                        'class' => Customer::class,
                        'label' => 'admin.windfarm.customer',
                        'disabled' => true,
                    )
                )
                ->add(
                    'isFromAudit',
                    null,
                    array(
                        'label' => 'admin.workorder.is_from_audit',
                        'disabled' => true,
                    )
                )
                ->end()
                ->with('admin.windfarm.title', $this->getFormMdSuccessBoxArray(4))
                ->add(
                    'windfarm',
                    EntityType::class,
                    array(
                        'class' => Windfarm::class,
                        'label' => 'admin.windfarm.title',
                        'disabled' => true,
                    )
                )
                ->add(
                    'repairAccessTypes',
                    ChoiceType::class,
                    array(
                        'label' => 'admin.workorder.repair_access_types',
                        'choices' => RepairAccessTypeEnum::getEnumArray(),
                        'multiple' => true,
                        'expanded' => false,
                        'required' => true,
                    )
                )
                ->end()
                ->with('admin.workorder.certifying_company_name', $this->getFormMdSuccessBoxArray(4))
                ->add(
                    'certifyingCompanyName',
                    null,
                    array(
                        'label' => 'admin.workorder.certifying_company_name',
                    )
                )
                ->add(
                    'certifyingCompanyContactPerson',
                    null,
                    array(
                        'label' => 'admin.workorder.certifying_company_contact_person_short',
                    )
                )
                ->add(
                    'certifyingCompanyPhone',
                    null,
                    array(
                        'label' => 'admin.workorder.certifying_company_phone_short',
                    )
                )
                ->add(
                    'certifyingCompanyEmail',
                    null,
                    array(
                        'label' => 'admin.workorder.certifying_company_email_short',
                    )
                )
                ->end()
                ->with('admin.workordertask.title', $this->getFormMdSuccessBoxArray(12))
                ->add(
                    'workOrderTasks',
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
                ->with('admin.deliverynote.title', $this->getFormMdSuccessBoxArray(12))
                ->add(
                    'deliveryNotes',
                    CollectionType::class,
                    array(
                        'label' => ' ',
                        'required' => false,
                        'btn_add' => false,
                        'error_bubbling' => true,
                        'type_options' => array(
                            'delete' => false,
                        ),
                    ),
                    array(
                        'edit' => 'inline',
                        'inline' => 'table',
                    )
                )
                ->end()
            ;
            if ($this->getSubject()->isFromAudit()) {
                $formMapper
                    ->with('admin.common.general', $this->getFormMdSuccessBoxArray(4))
                    ->add(
                        'audits',
                        ModelAutocompleteType::class,
                        array(
                            'label' => 'admin.audit.title',
                            'disabled' => true,
                            'required' => true,
                            'multiple' => true,
                            'btn_add' => false,
                            'property' => 'string',
                        )
                    )
                    ->end()
                ;
            }
        } else { // is in create mode
            $formMapper
                ->with('admin.common.general', $this->getFormMdSuccessBoxArray(4))
                ->add(
                    'customer',
                    ModelType::class,
                    array(
                        'label' => 'admin.windfarm.customer',
                        'required' => true,
                        'multiple' => false,
                        'btn_add' => false,
                        'query' => $this->cr->findEnabledSortedByNameQ(),
                    )
                )
                ->end()
                ->with('admin.windfarm.title', $this->getFormMdSuccessBoxArray(4))
                ->add(
                    'windfarm',
                    null,
                    array(
                        'label' => 'admin.windfarm.title',
                    )
                )
                ->add(
                    'repairAccessTypes',
                    ChoiceType::class,
                    array(
                        'label' => 'admin.workorder.repair_access_types',
                        'choices' => RepairAccessTypeEnum::getEnumArray(),
                        'multiple' => true,
                        'expanded' => false,
                        'required' => true,
                    )
                )
                ->end()
                ->with('admin.workorder.certifying_company_name', $this->getFormMdSuccessBoxArray(4))
                ->add(
                    'certifyingCompanyName',
                    null,
                    array(
                        'label' => 'admin.workorder.certifying_company_name',
                    )
                )
                ->add(
                    'certifyingCompanyContactPerson',
                    null,
                    array(
                        'label' => 'admin.workorder.certifying_company_contact_person',
                    )
                )
                ->add(
                    'certifyingCompanyPhone',
                    null,
                    array(
                        'label' => 'admin.workorder.certifying_company_phone',
                    )
                )
                ->add(
                    'certifyingCompanyEmail',
                    null,
                    array(
                        'label' => 'admin.workorder.certifying_company_email',
                    )
                )
                ->end();
        }
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add(
                'createdAt',
                'doctrine_orm_date',
                array(
                    'label' => 'admin.workorder.date',
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
                'projectNumber',
                null,
                array(
                    'label' => 'admin.workorder.project_number',
                )
            )
            ->add(
                'customer',
                null,
                array(
                    'label' => 'admin.windfarm.customer',
                ),
                EntityType::class,
                array(
                    'class' => Customer::class,
                    'query_builder' => $this->cr->findAllSortedByNameQB(),
                )
            )
            ->add(
                'isFromAudit',
                null,
                array(
                    'label' => 'admin.workorder.is_from_audit',
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
                'audits',
                null,
                array(
                    'label' => 'admin.audit.title',
                ),
                EntityType::class,
                array(
                    'class' => Audit::class,
                    'query_builder' => $this->ar->getAllAuditsJoinedSortedByBeginDateQB(),
                    'choice_label' => 'toStringWithoutJoins',
                )
            )
            ->add(
                'certifyingCompanyName',
                null,
                array(
                    'label' => 'admin.workorder.certifying_company_name',
                )
            )
            ->add(
                'certifyingCompanyContactPerson',
                null,
                array(
                    'label' => 'admin.workorder.certifying_company_contact_person',
                )
            )
            ->add(
                'certifyingCompanyPhone',
                null,
                array(
                    'label' => 'admin.workorder.certifying_company_phone',
                )
            )
            ->add(
                'certifyingCompanyEmail',
                null,
                array(
                    'label' => 'admin.workorder.certifying_company_email',
                )
            )
            ->add(
                'repairAccessTypes',
                null,
                array(
                    'label' => 'admin.workorder.repair_access_types',
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
                'createdAt',
                null,
                array(
                    'label' => 'admin.workorder.date',
                    'format' => 'd/m/Y',
                    'editable' => false,
                    'header_class' => 'text-center',
                    'row_align' => 'center',
                )
            )
            ->add(
                'projectNumber',
                null,
                array(
                    'label' => 'admin.workorder.project_number',
                    'editable' => false,
                    'header_class' => 'text-center',
                    'row_align' => 'center',
                )
            )
            ->add(
                'customer',
                null,
                array(
                    'label' => 'admin.windfarm.customer',
                    'sortable' => true,
                    'sort_field_mapping' => array('fieldName' => 'name'),
                    'sort_parent_association_mappings' => array(array('fieldName' => 'customer')),
                )
            )
            ->add(
                'isFromAudit',
                null,
                array(
                    'label' => 'admin.workorder.is_from_audit',
                    'editable' => false,
                    'header_class' => 'text-center',
                    'row_align' => 'center',
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
                'audits',
                null,
                array(
                    'label' => 'admin.audit.title',
                    'sortable' => true,
                    'sort_field_mapping' => array('fieldName' => 'name'),
                    'sort_parent_association_mappings' => array(array('fieldName' => 'audits')),
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
                        'pdf' => array('template' => 'Admin/Buttons/list__action_pdf_button.html.twig'),
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
                'projectNumber',
                null,
                array(
                    'label' => 'admin.workorder.project_number',
                )
            )
            ->add(
                'customer',
                null,
                array(
                    'label' => 'admin.windfarm.customer',
                )
            )
            ->add(
                'isFromAudit',
                null,
                array(
                    'label' => 'admin.workorder.is_from_audit',
                )
            )
            ->add(
                'audits',
                null,
                array(
                    'label' => 'admin.audit.title',
                )
            )
            ->end()
            ->with('admin.windfarm.title', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'windfarm',
                null,
                array(
                    'label' => 'admin.windfarm.title',
                )
            )
            ->add(
                'repairAccessTypes',
                null,
                array(
                    'label' => 'admin.workorder.repair_access_types',
                    'template' => 'Admin/Cells/show__repair_access_type.html.twig',
                )
            )
            ->end()
            ->with('admin.workorder.certifying_company_name', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'certifyingCompanyName',
                null,
                array(
                    'label' => 'admin.workorder.certifying_company_name',
                )
            )
            ->add(
                'certifyingCompanyContactPerson',
                null,
                array(
                    'label' => 'admin.workorder.certifying_company_contact_person_short',
                )
            )
            ->add(
                'certifyingCompanyPhone',
                null,
                array(
                    'label' => 'admin.workorder.certifying_company_phone_short',
                )
            )
            ->add(
                'certifyingCompanyEmail',
                null,
                array(
                    'label' => 'admin.workorder.certifying_company_email_short',
                )
            )
            ->end()
            ->with('admin.workordertask.title', $this->getFormMdSuccessBoxArray(12))
            ->add(
                'workOrderTasks',
                null,
                array(
                    'label' => 'admin.workordertasks.title',
                    'template' => 'Admin/Cells/show__work_order_tasks.html.twig',
                )
            )
            ->end()
            ->with('admin.deliverynote.title', $this->getFormMdSuccessBoxArray(12))
            ->add(
                'deliveryNotes',
                null,
                array(
                    'label' => 'admin.workordertasks.title',
                    'template' => 'Admin/Cells/list__delivery_notes.html.twig',
                )
            )
            ->end()
        ;
    }
}
