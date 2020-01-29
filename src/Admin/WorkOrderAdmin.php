<?php

namespace App\Admin;

use App\Entity\Audit;
use App\Entity\Customer;
use App\Entity\Windfarm;
use App\Enum\RepairAccessTypeEnum;
use App\Enum\WorkOrderStatusEnum;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\CollectionType;
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
        '_sort_by' => 'projectNumber',
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
            ->add('getWindmillsFromSelectedWindfarmsIds', 'get-windmills-from-selected-windfarms-ids')
            ->add('pdf', $this->getRouterIdParameter().'/pdf')
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        if ($this->id($this->getSubject())) {
            // is edit mode
            $formMapper
                ->with('admin.common.general', $this->getFormMdSuccessBoxArray(4))
                ->add(
                    'projectNumber',
                    null,
                    array(
                        'label' => 'admin.workorder.project_number_short',
                        'required' => true,
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
                    'status',
                    ChoiceType::class,
                    array(
                        'label' => 'admin.audit.status',
                        'choices' => WorkOrderStatusEnum::getReversedEnumArray(),
                        'multiple' => false,
                        'expanded' => false,
                        'required' => true,
                    )
                )
                ->add(
                    'isFromAudit',
                    null,
                    array(
                        'label' => 'admin.workorder.is_from_audit_short',
                        'disabled' => true,
                    )
                )
                ->end()
                ->with('admin.windfarm.title', $this->getFormMdSuccessBoxArray(4))
                ->add(
                    'windfarms',
                    EntityType::class,
                    array(
                        'label' => 'admin.workorder.windfarms',
                        'class' => Windfarm::class,
                        'query_builder' => $this->wfr->findCustomerEnabledSortedByNameQB($this->getSubject()->getCustomer()),
                        'required' => true,
                        'multiple' => true,
                    )
                )
                ->add(
                    'repairAccessTypes',
                    ChoiceType::class,
                    array(
                        'label' => 'admin.workorder.repair_access_types',
                        'choices' => RepairAccessTypeEnum::getEnumArray(),
                        'multiple' => true,
                        'expanded' => true,
                        'required' => false,
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
        } else {
            // is create mode (new)
            $formMapper
                ->with('admin.common.general', $this->getFormMdSuccessBoxArray(4))
                ->add(
                    'projectNumber',
                    null,
                    array(
                        'label' => 'admin.workorder.project_number_short',
                        'required' => true,
                    )
                )
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
                ->add(
                    'status',
                    ChoiceType::class,
                    array(
                        'label' => 'admin.audit.status',
                        'choices' => WorkOrderStatusEnum::getReversedEnumArray(),
                        'multiple' => false,
                        'expanded' => false,
                        'required' => true,
                    )
                )
                ->end()
                ->with('admin.windfarm.title', $this->getFormMdSuccessBoxArray(4))
                ->add(
                    'windfarms',
                    EntityType::class,
                    array(
                        'label' => 'admin.workorder.windfarms',
                        'class' => Windfarm::class,
                        'query_builder' => $this->wfr->findEnabledSortedByNameQB(),
                        'required' => true,
                        'multiple' => true,
                    )
                )
                ->add(
                    'repairAccessTypes',
                    ChoiceType::class,
                    array(
                        'label' => 'admin.workorder.repair_access_types',
                        'choices' => RepairAccessTypeEnum::getEnumArray(),
                        'multiple' => true,
                        'expanded' => true,
                        'required' => false,
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
                ->end()
            ;
        }
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add(
                'projectNumber',
                null,
                array(
                    'label' => 'admin.workorder.project_number_short',
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
                'windfarms',
                null,
                array(
                    'label' => 'admin.workorder.windfarms',
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
                'isFromAudit',
                null,
                array(
                    'label' => 'admin.workorder.is_from_audit_short',
                )
            )
            ->add(
                'status',
                null,
                array(
                    'label' => 'admin.audit.status',
                ),
                ChoiceType::class,
                array(
                    'expanded' => false,
                    'multiple' => false,
                    'choices' => WorkOrderStatusEnum::getReversedEnumArray(),
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
                'projectNumber',
                null,
                array(
                    'label' => 'admin.workorder.project_number_short',
                    'editable' => true,
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
                'windfarms',
                null,
                array(
                    'label' => 'admin.workorder.windfarms',
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
                'isFromAudit',
                null,
                array(
                    'label' => 'admin.workorder.is_from_audit_short',
                    'editable' => false,
                    'header_class' => 'text-center',
                    'row_align' => 'center',
                )
            )
            ->add(
                'status',
                null,
                array(
                    'label' => 'admin.audit.status',
                    'editable' => false,
                    'template' => 'Admin/Cells/list__cell_audit_status.html.twig',
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
                    'label' => 'admin.workorder.project_number_short',
                )
            )
            ->add(
                'statusString',
                null,
                array(
                    'label' => 'admin.audit.status',
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
                'audits',
                null,
                array(
                    'label' => 'admin.audit.title',
                )
            )
            ->add(
                'isFromAudit',
                null,
                array(
                    'label' => 'admin.workorder.is_from_audit_short',
                )
            )
            ->end()
            ->with('admin.windfarm.title', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'windfarms',
                null,
                array(
                    'label' => 'admin.workorder.windfarms',
                )
            )
            ->add(
                'repairAccessTypes',
                null,
                array(
                    'label' => 'admin.workorder.repair_access_types',
                    'template' => 'Admin/Cells/show__extends_repair_access_type.html.twig',
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
