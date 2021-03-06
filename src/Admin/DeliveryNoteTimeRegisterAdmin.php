<?php

namespace App\Admin;

use App\Enum\MinutesEnum;
use App\Entity\DeliveryNoteTimeRegister;
use App\Enum\TimeRegisterShiftEnum;
use App\Enum\TimeRegisterTypeEnum;
use DateTime;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;

/**
 * Class DeliveryNoteTimeRegisterAdmin.
 *
 * @category Admin
 */
class DeliveryNoteTimeRegisterAdmin extends AbstractBaseAdmin
{
    protected $classnameLabel = 'admin.deliverynotetimeregister.title';
    protected $baseRoutePattern = 'workorders/deliverynotetimeregister';
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
            $formMapper
                ->with('admin.common.general', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'deliveryNote',
                    null,
                    array(
                        'label' => 'admin.deliverynote.title',
                        'required' => true,
                    )
                )
                ->end()
            ;
        } else {
            // is embeded form in DeliveryNoteAdmin
            $formMapper
                ->with('admin.common.general', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'deliveryNote',
                    null,
                    array(
                        'label' => 'admin.deliverynote.title',
                        'required' => true,
                        'attr' => array(
                            'hidden' => true,
                        ),
                    )
                )
                ->end()
            ;
        }
        $formMapper
            ->with('admin.common.general', $this->getFormMdSuccessBoxArray(3))
            ->add(
                'type',
                ChoiceType::class,
                array(
                    'label' => 'admin.deliverynotetimeregister.type',
                    'choices' => TimeRegisterTypeEnum::getEnumArray(),
                    'multiple' => false,
                )
            )
            ->add(
                'shift',
                ChoiceType::class,
                array(
                    'label' => 'admin.deliverynotetimeregister.shift',
                    'choices' => TimeRegisterShiftEnum::getEnumArray(),
                    'multiple' => false,
                )
            )
            ->end()
            ->with('admin.common.controls', $this->getFormMdSuccessBoxArray(3))
            ->add(
                'begin',
                TimeType::class,
                array(
                    'label' => 'admin.deliverynotetimeregister.begin',
                    'widget' => 'choice',
                    'minutes' => MinutesEnum::getEnumArray(),
                )
            )
            ->add(
                'end',
                TimeType::class,
                array(
                    'label' => 'admin.deliverynotetimeregister.end',
                    'widget' => 'choice',
                    'minutes' => MinutesEnum::getEnumArray(),
                )
            )
            ->add(
                'comment',
                TextType::class,
                array(
                    'label' => 'admin.deliverynotetimeregister.comment',
                    'required' => false,
                )
            )
            ->add(
                'totalHours',
                null,
                array(
                    'label' => 'admin.deliverynotetimeregister.total_hours',
                    'attr' => array(
                        'disabled' => true,
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
                'deliveryNote',
                null,
                array(
                    'label' => 'admin.deliverynote.title',
                )
            )
            ->add(
                'type',
                null,
                array(
                    'label' => 'admin.deliverynotetimeregister.type',
                ),
                ChoiceType::class,
                array(
                    'expanded' => false,
                    'multiple' => false,
                    'choices' => TimeRegisterTypeEnum::getEnumArray(),
                )
            )
            ->add(
                'shift',
                null,
                array(
                    'label' => 'admin.deliverynotetimeregister.shift',
                ),
                ChoiceType::class,
                array(
                    'expanded' => false,
                    'multiple' => false,
                    'choices' => TimeRegisterShiftEnum::getEnumArray(),
                )
            )
            ->add(
                'begin',
                null,
                array(
                    'label' => 'admin.deliverynotetimeregister.begin',
                    'format' => 'h:m:s',
                ),
                TimeType::class,
                array(
                    'minutes' => MinutesEnum::getEnumArray(),
                )
            )
            ->add(
                'end',
                null,
                array(
                    'label' => 'admin.deliverynotetimeregister.end',
                    'format' => 'h:m:s',
                ),
                TimeType::class,
                array(
                    'minutes' => MinutesEnum::getEnumArray(),
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
                'deliveryNote',
                null,
                array(
                    'label' => 'admin.deliverynote.title',
                    'sortable' => true,
                    'sort_field_mapping' => array('fieldName' => 'id'),
                    'sort_parent_association_mappings' => array(array('fieldName' => 'deliveryNote')),
                    'editable' => false,
                    'header_class' => 'text-center',
                    'row_align' => 'center',
                )
            )
            ->add(
                'type',
                'string',
                array(
                    'label' => 'admin.deliverynotetimeregister.type',
                    'template' => 'Admin/Cells/list__cell_delivery_note_time_register_type.html.twig',
                    'editable' => false,
                    'header_class' => 'text-center',
                    'row_align' => 'center',
                )
            )
            ->add(
                'shift',
                'string',
                array(
                    'label' => 'admin.deliverynotetimeregister.shift',
                    'template' => 'Admin/Cells/list__cell_delivery_note_time_register_shift.html.twig',
                    'editable' => false,
                    'header_class' => 'text-center',
                    'row_align' => 'center',
                )
            )
            ->add(
                'begin',
                'date',
                array(
                    'label' => 'admin.deliverynotetimeregister.begin',
                    'format' => 'H:i',
                    'editable' => false,
                    'header_class' => 'text-center',
                    'row_align' => 'center',
                )
            )
            ->add(
                'end',
                'date',
                array(
                    'label' => 'admin.deliverynotetimeregister.end',
                    'format' => 'H:i',
                    'editable' => false,
                    'header_class' => 'text-center',
                    'row_align' => 'center',
                )
            )
            ->add(
                'totalHours',
                null,
                array(
                    'label' => 'admin.deliverynotetimeregister.total_hours',
                    'template' => 'Admin/Cells/list__cell_delivery_note_time_register_total_hours.html.twig',
                    'editable' => false,
                    'header_class' => 'text-right',
                    'row_align' => 'right',
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
                        'delete' => array('template' => 'Admin/Buttons/list__action_delete_button.html.twig'),
                    ),
                )
            )
        ;
    }

    /**
     * @param DeliveryNoteTimeRegister $object
     */
    public function prePersist($object)
    {
        $this->commonPreEvent($object);
    }

    /**
     * @param DeliveryNoteTimeRegister $object
     */
    public function preUpdate($object)
    {
        $this->commonPreEvent($object);
    }

    /**
     * @param DeliveryNoteTimeRegister $object
     */
    private function commonPreEvent($object)
    {
        if (!is_null($object->getBegin()) && !is_null($object->getEnd())) {
            if ($object->getBegin() instanceof DateTime && $object->getEnd() instanceof DateTime) {
                if ($object->getBegin()->format('H:i') < $object->getEnd()->format('H:i')) {
                    $time1 = strtotime($object->getBegin()->format('H:i:s'));
                    $time2 = strtotime($object->getEnd()->format('H:i:s'));
                    $object->setTotalHours(round(abs($time2 - $time1) / 3600, 2));
                }
            }
        }
    }
}
