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
use Symfony\Component\Form\Extension\Core\Type\FormType;

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
        /** @var WorkOrderTask $workOrderTask */
        $workOrderTask = $this->getSubject();
        /** @var WorkOrder $workOrder */
        $workOrder = $this->getRoot()->getSubject();
        $windfarms = [];
        $isParentObjectFromAudit = false;
        if ($workOrder instanceof WorkOrder) {
            /** @var Windfarm[]|array $windfarms */
            $windfarms = $workOrder->getWindfarms();
            $isParentObjectFromAudit = $workOrder->isFromAudit();
        }
        $hiddenAttrArray = ['hidden' => true];
        $isNewRecord = $this->id($this->getSubject()) ? false : true;
        if ($isNewRecord) {
            $isObjectFromAudit = false;
        } else {
            $isObjectFromAudit = $workOrderTask->isFromAudit();
        }
        $formMapper
            ->with('admin.common.general', $this->getFormMdSuccessBoxArray(3))
            ->add(
                'workOrder',
                null,
                array(
                    'label' => 'admin.workorder.title',
                    'required' => true,
                    'attr' => $hiddenAttrArray,
                )
            )
            ->add(
                'windmill',
                ModelType::class,
                array(
                    'label' => 'admin.windmill.title',
                    'property' => 'code',
                    'query' => $this->wmr->findMultipleByWindfarmsArrayQB($windfarms),
                    'btn_add' => false,
                    'required' => true,
                    'disabled' => $isObjectFromAudit,
                )
            )
            ->add(
                'windmillBlade',
                ModelType::class,
                array(
                    'label' => 'admin.blade.title',
                    'property' => 'order',
                    'query' => $this->wbr->findMultipleByWindfarmsArrayQB($windfarms),
                    'btn_add' => false,
                    'required' => false,
                    'disabled' => $isObjectFromAudit,
                )
            )
            ->add(
                'bladeDamage',
                ModelAutocompleteType::class,
                array(
                    'label' => 'admin.bladedamage.title',
                    'property' => 'damage.code',
                    'disabled' => $isObjectFromAudit,
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
                    'required' => false,
                    'disabled' => $isObjectFromAudit,
                )
            )
            ->add(
                'radius',
                null,
                array(
                    'label' => 'admin.bladedamage.radius',
                    'required' => false,
                    'disabled' => $isObjectFromAudit,
                )
            )
            ->add(
                'distance',
                null,
                array(
                    'label' => 'admin.bladedamage.distance',
                    'required' => false,
                    'disabled' => $isObjectFromAudit,
                )
            )
            ->add(
                'size',
                null,
                array(
                    'label' => 'admin.bladedamage.size',
                    'required' => false,
                    'disabled' => $isObjectFromAudit,
                )
            )
            ->add(
                'description',
                null,
                array(
                    'label' => 'admin.workordertask.description',
                    'required' => true,
                    'disabled' => $isObjectFromAudit,
                )
            )
            ->add(
                'multifiles',
                FormType::class,
                array(
                    'label' => 'admin.auditwindmillblade.photos',
                    'mapped' => false,
                    'required' => false,
                    'disabled' => false,
                    'attr' => ['class' => 'dropzone'],
                )
            )
            ->add(
                'isCompleted',
                null,
                array(
                    'label' => 'admin.workordertask.is_completed',
                    'required' => false,
                    'disabled' => false,
                )
            )
        ;
        if ($isParentObjectFromAudit) {
            $formMapper
                ->add(
                    'isFromAudit',
                    null,
                    array(
                        'label' => 'admin.auditwindmillblade.audit',
                        'required' => false,
                        'disabled' => true,
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
                    'label' => 'admin.blade.title',
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
                    'label' => 'admin.blade.title',
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
