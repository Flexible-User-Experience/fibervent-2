<?php

namespace App\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\ColorType;

/**
 * Class DamageCategoryAdmin.
 *
 * @category Admin
 */
class DamageCategoryAdmin extends AbstractBaseAdmin
{
    protected $classnameLabel = 'admin.damagecategory.title';
    protected $baseRoutePattern = 'audits/damage-category';
    protected $datagridValues = array(
        '_sort_by' => 'category',
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
                'category',
                null,
                array(
                    'label' => 'admin.damagecategory.category',
                    'required' => true,
                )
            )
            ->add(
                'priority',
                null,
                array(
                    'label' => 'admin.damagecategory.priority',
                    'required' => true,
                )
            )
            ->add(
                'description',
                null,
                array(
                    'label' => 'admin.damagecategory.description',
                    'required' => true,
                )
            )
            ->add(
                'recommendedAction',
                null,
                array(
                    'label' => 'admin.damagecategory.recomended_action',
                    'required' => true,
                )
            )
            ->end()
            ->with('admin.damage.translations', $this->getFormMdSuccessBoxArray(6))
//            ->add(
//                'translations',
//                TranslationsType::class,
//                array(
//                    'required' => false,
//                    'label' => ' ',
//                    'translatable_class' => DamageTranslation::class,
//                    'fields' => array(
//                        'priority' => array(
//                            'label' => 'admin.damagecategory.priority',
//                            'required' => false,
//                        ),
//                        'description' => array(
//                            'label' => 'admin.damagecategory.description',
//                            'attr' => array(
//                                'rows' => 8,
//                            ),
//                            'required' => false,
//                        ),
//                        'recommendedAction' => array('label' => 'admin.damagecategory.recomended_action', 'required' => false),
//                    ),
//                )
//            )
            ->end()
            ->with('admin.common.controls', $this->getFormMdSuccessBoxArray(6))
            ->add(
                'colour',
                ColorType::class,
                array(
                    'label' => 'admin.damagecategory.colour',
                    'required' => true,
                )
            )
            ->add(
                'enabled',
                null,
                array(
                    'label' => 'admin.common.enabled',
                    'required' => false,
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
                'fakecolour',
                null,
                array(
                    'label' => 'admin.damagecategory.colour',
                    'template' => 'Admin/Cells/list__cell_colour.html.twig',
                    'editable' => false,
                )
            )
            ->add(
                'category',
                null,
                array(
                    'label' => 'admin.damagecategory.category',
                    'editable' => true,
                )
            )
            ->add(
                'priority',
                null,
                array(
                    'label' => 'admin.damagecategory.priority',
                    'editable' => true,
                )
            )
            ->add(
                'description',
                null,
                array(
                    'label' => 'admin.damagecategory.description',
                    'editable' => true,
                )
            )
            ->add(
                'recommendedAction',
                null,
                array(
                    'label' => 'admin.damagecategory.recomended_action',
                    'editable' => true,
                )
            )
            ->add(
                'enabled',
                null,
                array(
                    'label' => 'admin.common.enabled',
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
