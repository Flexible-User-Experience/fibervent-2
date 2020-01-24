<?php

namespace App\Admin;

use App\Entity\PresenceMonitoring;
use App\Enum\MinutesEnum;
use App\Enum\PresenceMonitoringCategoryEnum;
use App\Entity\User;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\Form\Type\DatePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;

/**
 * Class WorkOrderTaskAdmin.
 *
 * @category Admin
 */
class PresenceMonitoringAdmin extends AbstractBaseAdmin
{
    protected $classnameLabel = 'admin.presencemonitoring.title';
    protected $baseRoutePattern = 'presencemonitoring';
    protected $datagridValues = array(
        '_sort_by' => 'date',
        '_sort_order' => 'desc',
    );

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
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
                'worker',
                EntityType::class,
                array(
                    'label' => 'admin.presencemonitoring.worker',
                    'class' => User::class,
                    'query_builder' => $this->ur->findAllSortedByNameQB(),
                    'choice_label' => 'fullnameCanonical',
                )
            )
            ->add(
                'category',
                ChoiceType::class,
                array(
                    'label' => 'admin.presencemonitoring.category',
                    'choices' => PresenceMonitoringCategoryEnum::getEnumArray(),
                    'multiple' => false,
                    'expanded' => false,
                    'required' => true,
                )
            )
            ->end()
            ->with('admin.common.details', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'morningHourBegin',
                TimeType::class,
                array(
                    'label' => 'admin.presencemonitoring.morning_hour_begin',
                    'minutes' => MinutesEnum::getQuartersEnumArray(),
                    'required' => false,
                )
            )
            ->add(
                'morningHourEnd',
                TimeType::class,
                array(
                    'label' => 'admin.presencemonitoring.morning_hour_end',
                    'minutes' => MinutesEnum::getQuartersEnumArray(),
                    'required' => false,
                )
            )
            ->add(
                'afternoonHourBegin',
                TimeType::class,
                array(
                    'label' => 'admin.presencemonitoring.afternoon_hour_begin',
                    'minutes' => MinutesEnum::getQuartersEnumArray(),
                    'required' => false,
                )
            )
            ->add(
                'afternoonHourEnd',
                TimeType::class,
                array(
                    'label' => 'admin.presencemonitoring.afternoon_hour_end',
                    'minutes' => MinutesEnum::getQuartersEnumArray(),
                    'required' => false,
                )
            )
            ->end()
            ->with('admin.common.totals', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'normalHours',
                ChoiceType::class,
                array(
                    'label' => 'admin.presencemonitoring.normal_hours',
                    'choices' => MinutesEnum::getChoicesQuartersEnumArray(),
                    'multiple' => false,
                    'expanded' => false,
                    'required' => true,
                    'attr' => array(
                        'disabled' => true,
                    ),
                )
            )
            ->add(
                'extraHours',
                ChoiceType::class,
                array(
                    'label' => 'admin.presencemonitoring.extra_hours',
                    'choices' => MinutesEnum::getChoicesQuartersEnumArray(),
                    'multiple' => false,
                    'expanded' => false,
                    'required' => true,
                    'attr' => array(
                        'disabled' => true,
                    ),
                )
            )
            ->add(
                'totalHours',
                ChoiceType::class,
                array(
                    'label' => 'admin.presencemonitoring.total_hours',
                    'choices' => MinutesEnum::getChoicesQuartersEnumArray(),
                    'multiple' => false,
                    'expanded' => false,
                    'required' => true,
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
                'worker',
                null,
                array(
                    'label' => 'admin.workertimesheet.worker',
                ),
                EntityType::class,
                array(
                    'class' => User::class,
                    'query_builder' => $this->ur->findAllSortedByNameQB(),
                    'choice_label' => 'fullnameCanonical',
                )
            )
            ->add(
                'category',
                null,
                array(
                    'label' => 'admin.presencemonitoring.category',
                ),
                ChoiceType::class,
                array(
                    'choices' => PresenceMonitoringCategoryEnum::getEnumArray(),
                    'multiple' => false,
                    'expanded' => false,
                    'required' => false,
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
                    'editable' => false,
                    'header_class' => 'text-center',
                    'row_align' => 'center',
                )
            )
            ->add(
                'worker',
                null,
                array(
                    'label' => 'admin.presencemonitoring.worker',
                    'sortable' => true,
                    'sort_field_mapping' => array('fieldName' => 'firstname'),
                    'sort_parent_association_mappings' => array(array('fieldName' => 'worker')),
                )
            )
            ->add(
                'category',
                null,
                array(
                    'label' => 'admin.presencemonitoring.category',
                    'template' => 'Admin/Cells/list__cell_presence_monitoring_category.html.twig',
                    'editable' => false,
                    'header_class' => 'text-center',
                    'row_align' => 'center',
                )
            )
            ->add(
                'morningHourBegin',
                'date',
                array(
                    'label' => 'admin.presencemonitoring.morning_hour_begin',
                    'format' => 'H:i',
                    'editable' => false,
                    'header_class' => 'text-center',
                    'row_align' => 'center',
                )
            )
            ->add(
                'morningHourEnd',
                'date',
                array(
                    'label' => 'admin.presencemonitoring.morning_hour_end',
                    'format' => 'H:i',
                    'editable' => false,
                    'header_class' => 'text-center',
                    'row_align' => 'center',
                )
            )
            ->add(
                'afternoonHourBegin',
                'date',
                array(
                    'label' => 'admin.presencemonitoring.afternoon_hour_begin',
                    'format' => 'H:i',
                    'editable' => false,
                    'header_class' => 'text-center',
                    'row_align' => 'center',
                )
            )
            ->add(
                'afternoonHourEnd',
                'date',
                array(
                    'label' => 'admin.presencemonitoring.afternoon_hour_end',
                    'format' => 'H:i',
                    'editable' => false,
                    'header_class' => 'text-center',
                    'row_align' => 'center',
                )
            )
            ->add(
                'normalHours',
                null,
                array(
                    'label' => 'admin.presencemonitoring.normal_hours',
                    'template' => 'Admin/Cells/list__cell_presence_monitoring_normal_hours.html.twig',
                    'editable' => false,
                    'header_class' => 'text-right',
                    'row_align' => 'right',
                )
            )
            ->add(
                'extraHours',
                null,
                array(
                    'label' => 'admin.presencemonitoring.extra_hours',
                    'template' => 'Admin/Cells/list__cell_presence_monitoring_extra_hours.html.twig',
                    'editable' => false,
                    'header_class' => 'text-right',
                    'row_align' => 'right',
                )
            )
            ->add(
                'totalHours',
                null,
                array(
                    'label' => 'admin.presencemonitoring.total_hours',
                    'template' => 'Admin/Cells/list__cell_presence_monitoring_total_hours.html.twig',
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
     * @param object|PresenceMonitoring $object
     */
    function prePersist($object)
    {
        $this->commonUpdates($object);
    }

    /**
     * @param object|PresenceMonitoring $object
     */
    function preUpdate($object)
    {
        $this->commonUpdates($object);
    }

    /**
     * @param PresenceMonitoring $object
     */
    private function commonUpdates($object)
    {
        $object
            ->setTotalHours($object->getDifferenceBetweenEndAndBeginHoursInDecimalHours())
            ->setNormalHours($object->getNormalHoursDifferenceFromTotal())
            ->setExtraHours($object->getExtraHoursDifferenceFromTotal())
        ;
    }
}
