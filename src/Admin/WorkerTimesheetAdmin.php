<?php

namespace App\Admin;

use App\Entity\DeliveryNote;
use App\Entity\User;
use App\Entity\WorkerTimesheet;
use App\Enum\UserRolesEnum;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 * Class WorkOrderTaskAdmin.
 *
 * @category Admin
 */
class WorkerTimesheetAdmin extends AbstractBaseAdmin
{
    protected $classnameLabel = 'admin.workertimesheet.title';
    protected $baseRoutePattern = 'workorders/workertimesheet';
    protected $datagridValues = array(
        '_sort_by' => 'id',
        '_sort_order' => 'desc',
    );

    /**
     * Configure route collection.
     *
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('show');
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
                ->andWhere($ra.'.worker = :worker')
                ->setParameter('worker', $user)
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
        $deliveryNoteQueryBuilder = $this->dnr->findAllSortedByDateDescQB();
        if (!$isNewRecord) {
            /** @var WorkerTimesheet $workerTimesheet */
            $workerTimesheet = $this->getSubject();
            if ($workerTimesheet->getWorker()->hasRole(UserRolesEnum::ROLE_OPERATOR) || $workerTimesheet->getWorker()->hasRole(UserRolesEnum::ROLE_TECHNICIAN)) {
                $deliveryNoteQueryBuilder = $this->dnr->findAllRelatedToWorkerSortedByDateDescQB($workerTimesheet->getWorker());
            }
        } else {
            /** @var User $worker */
            $worker = $this->tss->getToken()->getUser();
            if ($worker->hasRole(UserRolesEnum::ROLE_OPERATOR) || $worker->hasRole(UserRolesEnum::ROLE_TECHNICIAN)) {
                $deliveryNoteQueryBuilder = $this->dnr->findAllRelatedToWorkerSortedByDateDescQB($worker);
            }
        }
        $formMapper
            ->with('admin.common.general', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'deliveryNote',
                EntityType::class,
                array(
                    'label' => 'admin.deliverynote.title',
                    'class' => DeliveryNote::class,
                    'expanded' => false,
                    'required' => true,
                    'query_builder' => $deliveryNoteQueryBuilder,
                )
            )
            ->add(
                'worker',
                EntityType::class,
                array(
                    'label' => 'admin.workertimesheet.worker',
                    'class' => User::class,
                    'expanded' => false,
                    'required' => true,
                    'query_builder' => $this->ur->getEnabledWorkersSortedByNameQB(),
                )
            )
            ->add(
                'workDescription',
                null,
                array(
                    'label' => 'admin.workertimesheet.work_description',
                )
            )
            ->end()
            ->with('admin.common.details', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'totalNormalHours',
                null,
                array(
                    'label' => 'admin.workertimesheet.total_normal_hours',
                )
            )
            ->add(
                'totalVerticalHours',
                null,
                array(
                    'label' => 'admin.workertimesheet.total_vertical_hours',
                )
            )
            ->add(
                'totalInclementWeatherHours',
                null,
                array(
                    'label' => 'admin.workertimesheet.total_inclement_weather_hours',
                )
            )
            ->add(
                'totalTripHours',
                null,
                array(
                    'label' => 'admin.workertimesheet.total_trip_hours',
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
                ),
                EntityType::class,
                array(
                    'class' => DeliveryNote::class,
                    'query_builder' => $this->dnr->findAllSortedByDateDescQB(),
                )
            )
            ->add('worker',
                null,
                array(
                    'label' => 'admin.workertimesheet.worker',
                ),
                EntityType::class,
                array(
                    'class' => User::class,
                    'query_builder' => $this->ur->getEnabledWorkersSortedByNameQB(),
                )
            )
            ->add('workDescription',
                null,
                array(
                    'label' => 'admin.workertimesheet.work_description',
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
                    'associated_property' => 'id',
                    'sortable' => true,
                    'sort_field_mapping' => array('fieldName' => 'id'),
                    'sort_parent_association_mappings' => array(array('fieldName' => 'deliveryNote')),
                    'header_class' => 'text-center',
                    'row_align' => 'center',
                )
            )
            ->add(
                'worker',
                null,
                array(
                    'label' => 'admin.workertimesheet.worker',
                    'sortable' => true,
                    'sort_field_mapping' => array('fieldName' => 'firstname'),
                    'sort_parent_association_mappings' => array(array('fieldName' => 'worker')),
                )
            )
            ->add(
                'workDescription',
                null,
                array(
                    'label' => 'admin.workertimesheet.work_description',
                    'editable' => true,
                )
            )
            ->add(
                'totalNormalHours',
                null,
                array(
                    'label' => 'admin.workertimesheet.total_normal_hours',
                    'editable' => true,
                    'header_class' => 'text-right',
                    'row_align' => 'right',
                )
            )
            ->add(
                'totalVerticalHours',
                null,
                array(
                    'label' => 'admin.workertimesheet.total_vertical_hours',
                    'editable' => true,
                    'header_class' => 'text-right',
                    'row_align' => 'right',
                )
            )
            ->add(
                'totalInclementWeatherHours',
                null,
                array(
                    'label' => 'admin.workertimesheet.total_inclement_weather_hours',
                    'editable' => true,
                    'header_class' => 'text-right',
                    'row_align' => 'right',
                )
            )
            ->add(
                'totalTripHours',
                null,
                array(
                    'label' => 'admin.workertimesheet.total_trip_hours',
                    'editable' => true,
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
}
