<?php

namespace App\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\Form\Type\CollectionType;

/**
 * Class DamageAdmin.
 *
 * @category Admin
 */
class DamageAdmin extends AbstractBaseAdmin
{
    protected $maxPerPage = 50;
    protected $classnameLabel = 'admin.damage.title';
    protected $baseRoutePattern = 'audits/damage';
    protected $datagridValues = array(
        '_sort_by' => 'code',
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
            ->with('admin.common.general', $this->getFormMdSuccessBoxArray(6))
            ->add(
                'section',
                null,
                array(
                    'label' => 'admin.damage.section',
                    'required' => true,
                )
            )
            ->add(
                'code',
                null,
                array(
                    'label' => 'admin.damage.code',
                    'required' => true,
                )
            )
            ->add(
                'description',
                null,
                array(
                    'label' => 'admin.damage.description',
                    'required' => true,
                )
            )
            ->end()
            ->with('admin.common.controls', $this->getFormMdSuccessBoxArray(3))
            ->add(
                'enabled',
                null,
                array(
                    'label' => 'admin.common.enabled',
                    'required' => false,
                )
            )
            ->end()
            ->with('admin.damage.translations', $this->getFormMdSuccessBoxArray(12))
            ->add(
                'translations',
                CollectionType::class,
                array(
                    'label' => ' ',
                    'required' => false,
                    'btn_add' => true,
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
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add(
                'section',
                null,
                array(
                    'label' => 'admin.damage.section',
                    'editable' => true,
                    'header_class' => 'text-right',
                    'row_align' => 'right',
                )
            )
            ->add(
                'code',
                null,
                array(
                    'label' => 'admin.damage.code',
                    'editable' => true,
                    'header_class' => 'text-right',
                    'row_align' => 'right',
                )
            )
            ->add(
                'description',
                null,
                array(
                    'label' => 'admin.damage.description',
                    'editable' => true,
                )
            )
            ->add(
                'enabled',
                null,
                array(
                    'label' => 'admin.common.enabled',
                    'editable' => true,
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
