<?php

namespace App\Admin;

use App\Entity\Windfarm;
use App\Entity\WorkOrder;
use App\Entity\WorkOrderTask;
use App\Enum\BladeDamagePositionEnum;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Class WorkOrderTaskAdmin.
 *
 * @category Admin
 */
class WorkOrderTaskAdmin extends AbstractBaseAdmin
{
    protected $classnameLabel = 'admin.workordertask.title';
    protected $baseRoutePattern = 'workorders/workordertask';
    protected $datagridValues = array(
        '_sort_by' => 'id',
        '_sort_order' => 'desc',
    );

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        if ($this->getRootCode() == $this->getCode()) {
            // not embeded
            $formMapper
                ->with('admin.common.general', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'workOrder',
                    null,
                    array(
                        'label' => 'admin.workorder.title',
                    )
                )
                ->end()
            ;
        } else {
            // embeded
            $formMapper
                ->with('admin.common.general', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'workOrder',
                    null,
                    array(
                        'label' => 'admin.workorder.title',
                        'attr' => array(
                            'hidden' => true,
                        ),
                    )
                )
                ->end()
            ;
            /** @var WorkOrder $workOrder */
            $workOrder = $this->getRoot()->getSubject();
            /** @var Windfarm[]|array $windfarms */
            $windfarms = $workOrder->getWindfarms();
            /** @var WorkOrderTask $workOrderTask */
            $workOrderTask = $this->getSubject();
        }
        if ($this->id($this->getSubject())) {
            // is in edit mode
            if ($this->getSubject()->isFromAudit()) {
                // is in edit mode from audit
                $formMapper
                    ->with('admin.bladedamage.title', $this->getFormMdSuccessBoxArray(5))
                    ->add(
                        'windmill',
                        ModelType::class,
                        array(
                            'label' => 'admin.windmill.title',
                            'btn_add' => false,
                            'required' => false,
                            'disabled' => true,
                            'query' => $this->wmr->findMultipleByWindfarmsArrayQB($windfarms),
                        )
                    )
                    ->add(
                        'windmillBlade',
                        ModelType::class,
                        array(
                            'label' => 'admin.windmillblade.title',
                            'btn_add' => false,
                            'required' => false,
                            'disabled' => true,
                            'query' => $this->wbr->findWindmillSortedByCodeQB($workOrderTask->getWindmill()),
                        )
                    )
                    ->add(
                        'bladeDamage',
                        ModelAutocompleteType::class,
                        array(
                            'label' => 'admin.bladedamage.title',
                            'disabled' => true,
                            'property' => 'damage.code',
                        )
                    )
                    ->add(
                        'position',
                        ChoiceType::class,
                        array(
                            'label' => 'admin.bladedamage.position',
                            'disabled' => true,
                            'choices' => BladeDamagePositionEnum::getEnumArray(),
                            'multiple' => false,
                            'expanded' => false,
                        )
                    )
                    ->add(
                        'radius',
                        null,
                        array(
                            'label' => 'admin.bladedamage.radius',
                            'disabled' => true,
                        )
                    )
                    ->add(
                        'distance',
                        null,
                        array(
                            'label' => 'admin.bladedamage.distance',
                            'disabled' => true,
                        )
                    )
                    ->add(
                        'size',
                        null,
                        array(
                            'label' => 'admin.bladedamage.size',
                            'disabled' => true,
                        )
                    )
                    ->end()
                ;
            } else {
                // is in edit mode NOT from audit
                $formMapper
                    ->with('admin.bladedamage.title', $this->getFormMdSuccessBoxArray(5))
                    ->add(
                        'windmill',
                        ModelType::class,
                        array(
                            'label' => 'admin.windmill.title',
                            'btn_add' => false,
                            'required' => false,
                            'property' => 'code',
                            'query' => $this->wmr->findMultipleByWindfarmsArrayQB($windfarms),
                        )
                    )
                    ->add(
                        'windmillBlade',
                        ModelType::class,
                        array(
                            'label' => 'admin.windmillblade.title',
                            'btn_add' => false,
                            'required' => false,
                            'query' => $this->wbr->findWindmillSortedByCodeQB($workOrderTask->getWindmill()),
                        )
                    )
                    ->add(
                        'bladeDamage',
                        ModelAutocompleteType::class,
                        array(
                            'label' => 'admin.bladedamage.title',
                            'disabled' => true,
                            'property' => 'damage.code',
                            'required' => true,
                        )
                    )
                    ->add(
                        'position',
                        ChoiceType::class,
                        array(
                            'label' => 'admin.bladedamage.position',
                            'choices' => BladeDamagePositionEnum::getEnumArray(),
                            'multiple' => false,
                            'expanded' => false,
                            'required' => true,
                        )
                    )
                    ->add(
                        'radius',
                        null,
                        array(
                            'label' => 'admin.bladedamage.radius',
                            'required' => true,
                        )
                    )
                    ->add(
                        'distance',
                        null,
                        array(
                            'label' => 'admin.bladedamage.distance',
                            'required' => true,
                        )
                    )
                    ->add(
                        'size',
                        null,
                        array(
                            'label' => 'admin.bladedamage.size',
                            'required' => true,
                        )
                    )
                    ->end()
                ;
            }
        } else {
            // is in create or new mode
            $formMapper
                ->with('admin.bladedamage.title', $this->getFormMdSuccessBoxArray(5))
                ->add(
                    'windmill',
                    ModelType::class,
                    array(
                        'label' => 'admin.windmill.title',
                        'btn_add' => false,
                        'required' => false,
                        'property' => 'code',
                        'query' => $this->wmr->findEnabledSortedByCustomerWindfarmAndWindmillCodeQB(),
                    )
                )
                ->add(
                    'windmillBlade',
                    ModelType::class,
                    array(
                        'label' => 'admin.windmillblade.title',
                        'btn_add' => false,
                        'required' => false,
                    )
                )
                ->add(
                    'bladeDamage',
                    ModelAutocompleteType::class,
                    array(
                        'label' => 'admin.bladedamage.title',
                        'disabled' => true,
                        'property' => 'damage.code',
                    )
                )
                ->add(
                    'position',
                    ChoiceType::class,
                    array(
                        'label' => 'admin.bladedamage.position',
                        'choices' => BladeDamagePositionEnum::getEnumArray(),
                        'multiple' => false,
                        'expanded' => false,
                        'required' => true,
                    )
                )
                ->add(
                    'radius',
                    null,
                    array(
                        'label' => 'admin.bladedamage.radius',
                        'required' => true,
                    )
                )
                ->add(
                    'distance',
                    null,
                    array(
                        'label' => 'admin.bladedamage.distance',
                        'required' => true,
                    )
                )
                ->add(
                    'size',
                    null,
                    array(
                        'label' => 'admin.bladedamage.size',
                        'required' => true,
                    )
                )
                ->end()
            ;
        }
        $formMapper
            ->with('admin.common.general', $this->getFormMdSuccessBoxArray(7))
            ->add('description',
                null,
                array(
                    'label' => 'admin.workordertask.description',
                )
            )
            ->add('isCompleted',
                null,
                array(
                    'label' => 'admin.workordertask.is_completed',
                )
            )
            ->add('isFromAudit',
                null,
                array(
                    'label' => 'admin.workorder.is_from_audit',
                    'disabled' => true,
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
                'workOrder',
                null,
                array(
                    'label' => 'admin.workorder.title',
                    'sortable' => true,
                    'sort_field_mapping' => array('fieldName' => 'projectNumber'),
                    'sort_parent_association_mappings' => array(array('fieldName' => 'workOrder')),
                )
            )
            ->add('isFromAudit',
                null,
                array(
                    'label' => 'admin.workorder.is_from_audit',
                )
            )
            ->add('windmillBlade',
                null,
                array(
                    'label' => 'admin.windmillblade.title',
                )
            )
            ->add('windmill',
                null,
                array(
                    'label' => 'admin.windmill.title',
                )
            )
            ->add('bladeDamage',
                null,
                array(
                    'label' => 'admin.bladedamage.title',
                )
            )
            ->add(
                'position',
                null,
                array(
                    'label' => 'admin.bladedamage.position',
                )
            )
            ->add(
                'radius',
                null,
                array(
                    'label' => 'admin.bladedamage.radius',
                )
            )
            ->add(
                'distance',
                null,
                array(
                    'label' => 'admin.bladedamage.distance',
                )
            )
            ->add(
                'size',
                null,
                array(
                    'label' => 'admin.bladedamage.size',
                )
            )
            ->add('isCompleted',
                null,
                array(
                    'label' => 'admin.workordertask.is_completed',
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
                'workOrder',
                null,
                array(
                    'label' => 'admin.workorder.title',
                    'sortable' => true,
                    'sort_field_mapping' => array('fieldName' => 'projectNumber'),
                    'sort_parent_association_mappings' => array(array('fieldName' => 'workOrder')),
                    'header_class' => 'text-center',
                    'row_align' => 'center',
                )
            )
            ->add('isFromAudit',
                null,
                array(
                    'label' => 'admin.workorder.is_from_audit',
                    'editable' => false,
                    'header_class' => 'text-center',
                    'row_align' => 'center',
                )
            )
            ->add('windmillBlade',
                null,
                array(
                    'label' => 'admin.windmillblade.title',
                    'sortable' => true,
                    'sort_field_mapping' => array('fieldName' => 'code'),
                    'sort_parent_association_mappings' => array(array('fieldName' => 'windmillBlade')),
                    'header_class' => 'text-center',
                    'row_align' => 'center',
                )
            )
            ->add('windmill',
                null,
                array(
                    'label' => 'admin.windmill.title',
                    'sortable' => true,
                    'sort_field_mapping' => array('fieldName' => 'code'),
                    'sort_parent_association_mappings' => array(array('fieldName' => 'windmill')),
                )
            )
            ->add('bladeDamage',
                null,
                array(
                    'label' => 'admin.bladedamage.title',
                    'sortable' => true,
                    'sort_field_mapping' => array('fieldName' => 'damage'),
                    'sort_parent_association_mappings' => array(array('fieldName' => 'bladeDamage')),
                    'header_class' => 'text-center',
                    'row_align' => 'center',
                )
            )
            ->add(
                'position',
                null,
                array(
                    'label' => 'admin.bladedamage.position',
                    'editable' => false,
                    'header_class' => 'text-right',
                    'row_align' => 'right',
                )
            )
            ->add(
                'radius',
                null,
                array(
                    'label' => 'admin.bladedamage.radius',
                    'editable' => false,
                    'header_class' => 'text-right',
                    'row_align' => 'right',
                )
            )
            ->add(
                'distance',
                null,
                array(
                    'label' => 'admin.bladedamage.distance',
                    'editable' => false,
                    'header_class' => 'text-right',
                    'row_align' => 'right',
                )
            )
            ->add(
                'size',
                null,
                array(
                    'label' => 'admin.bladedamage.size',
                    'editable' => false,
                    'header_class' => 'text-right',
                    'row_align' => 'right',
                )
            )
            ->add('isCompleted',
                null,
                array(
                    'label' => 'admin.workordertask.is_completed',
                    'editable' => false,
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
                    ),
                )
            )
        ;
    }
}
