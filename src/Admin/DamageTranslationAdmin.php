<?php

namespace App\Admin;

use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class DamageTranslationAdmin.
 *
 * @category Admin
 */
class DamageTranslationAdmin extends AbstractBaseAdmin
{
    protected $classnameLabel = 'admin.damagetranslation.title';
    protected $baseRoutePattern = 'audits/damage-translation';
    protected $datagridValues = array(
        '_sort_by' => 'id',
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
                'object',
                null,
                array(
                    'label' => 'admin.damagetranslation.object',
                    'required' => true,
                )
            )
            ->add(
                'locale',
                null,
                array(
                    'label' => 'admin.damagetranslation.locale',
                    'required' => true,
                )
            )
            ->add(
                'field',
                null,
                array(
                    'label' => 'admin.damagetranslation.field',
                    'required' => true,
                )
            )
            ->add(
                'content',
                TextType::class,
                array(
                    'label' => 'admin.damagetranslation.content',
                    'required' => true,
                )
            )
            ->end();
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add(
                'object',
                null,
                array(
                    'label' => 'admin.damagetranslation.object',
                )
            )
            ->add(
                'locale',
                null,
                array(
                    'label' => 'admin.damagetranslation.locale',
                )
            )
            ->add(
                'field',
                null,
                array(
                    'label' => 'admin.damagetranslation.field',
                )
            )
            ->add(
                'content',
                null,
                array(
                    'label' => 'admin.damagetranslation.content',
                )
            );
    }
    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add(
                'object',
                null,
                array(
                    'label' => 'admin.damagetranslation.object',
                    'associated_property' => 'extendedToString',
                    'editable' => true,
                )
            )
            ->add(
                'locale',
                null,
                array(
                    'label' => 'admin.damagetranslation.locale',
                    'editable' => true,
                )
            )
            ->add(
                'field',
                null,
                array(
                    'label' => 'admin.damagetranslation.field',
                    'editable' => true,
                )
            )
            ->add(
                'content',
                null,
                array(
                    'label' => 'admin.damagetranslation.content',
                    'editable' => true,
                )
            )
            ->add(
                '_action',
                'actions',
                array(
                    'label' => 'admin.common.action',
                    'actions' => array(
                        'edit' => array('template' => 'Admin/Buttons/list__action_edit_button.html.twig'),
                    ),
                )
            );
    }
}
