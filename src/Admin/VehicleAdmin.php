<?php

namespace App\Admin;

use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * Class VehicleAdmin.
 *
 * @category Admin
 */
class VehicleAdmin extends AbstractBaseAdmin
{
    protected $classnameLabel = 'admin.vehicle.title';
    protected $baseRoutePattern = 'workorders/vehicle';
    protected $datagridValues = array(
        '_sort_by' => 'name',
        '_sort_order' => 'asc',
    );

    /**
     * Configure route collection.
     *
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection->remove('delete');
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('admin.vehicle.title', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'name',
                null,
                array(
                    'label' => 'admin.vehicle.name',
                )
            )
            ->add('licensePlate',
                null,
                array(
                    'label' => 'admin.vehicle.licence_plate',
                )
            )
            ->end()
            ->with('admin.common.controls', $this->getFormMdSuccessBoxArray(3))
            ->add('active',
                null,
                array(
                    'label' => 'admin.vehicle.active',
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
                'name',
                null,
                array(
                    'label' => 'admin.vehicle.name',
                )
            )
            ->add('licensePlate',
                null,
                array(
                    'label' => 'admin.vehicle.licence_plate',
                )
            )
            ->add('active',
                null,
                array(
                    'label' => 'admin.vehicle.active',
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
                'name',
                null,
                array(
                    'label' => 'admin.vehicle.name',
                    'editable' => true,
                )
            )
            ->add('licensePlate',
                null,
                array(
                    'label' => 'admin.vehicle.licence_plate',
                    'editable' => true,
                )
            )
            ->add('active',
                null,
                array(
                    'label' => 'admin.vehicle.active',
                    'editable' => true,
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
