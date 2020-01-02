<?php

namespace App\Admin;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use App\Entity\Translations\DamageTranslation;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * Class DamageCategoryTranslationAdmin.
 *
 * @category Admin
 */
class DamageCategoryTranslationAdmin extends AbstractBaseAdmin
{
    protected $classnameLabel = 'admin.damagecategorytranslation.title';
    protected $baseRoutePattern = 'audits/damage-category-translation';
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
                    'label' => 'admin.damagecategorytranslation.object',
                    'required' => true,
                )
            )
            ->add(
                'locale',
                null,
                array(
                    'label' => 'admin.damagecategorytranslation.locale',
                    'required' => true,
                )
            )
            ->add(
                'field',
                null,
                array(
                    'label' => 'admin.damagecategorytranslation.field',
                    'required' => true,
                )
            )
            ->add(
                'content',
                null,
                array(
                    'label' => 'admin.damagecategorytranslation.content',
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
                    'label' => 'admin.damagecategorytranslation.object',
                )
            )
            ->add(
                'locale',
                null,
                array(
                    'label' => 'admin.damagecategorytranslation.locale',
                )
            )
            ->add(
                'field',
                null,
                array(
                    'label' => 'admin.damagecategorytranslation.field',
                )
            )
            ->add(
                'content',
                null,
                array(
                    'label' => 'admin.damagecategorytranslation.content',
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
                    'label' => 'admin.damagecategorytranslation.object',
                    'associated_property' => 'extendedToString',
                    'editable' => true,
                )
            )
            ->add(
                'locale',
                null,
                array(
                    'label' => 'admin.damagecategorytranslation.locale',
                    'editable' => true,
                )
            )
            ->add(
                'field',
                null,
                array(
                    'label' => 'admin.damagecategorytranslation.field',
                    'editable' => true,
                )
            )
            ->add(
                'content',
                null,
                array(
                    'label' => 'admin.damagecategorytranslation.content',
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
