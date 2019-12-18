<?php

namespace App\Admin;

use App\Enum\BladeDamageEdgeEnum;
use App\Enum\BladeDamagePositionEnum;
use App\Form\Type\ActionButtonFormType;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\Form\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

/**
 * Class BladeDamageAdmin.
 *
 * @category Admin
 */
class BladeDamageAdmin extends AbstractBaseAdmin
{
    protected $maxPerPage = 50;
    protected $classnameLabel = 'admin.bladedamage.title';
    protected $baseRoutePattern = 'audits/blade-damage';
    protected $datagridValues = array(
        '_sort_by' => 'status',
        '_sort_order' => 'desc',
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
        $formMapper->with('admin.common.general', $this->getFormMdSuccessBoxArray(3));
        if ($this->getRootCode() != $this->getCode()) {
            // only available on embeded admin
            $formMapper
                ->add(
                    'id',
                    NumberType::class,
                    array(
                        'label' => 'admin.bladedamage.number',
                        'required' => false,
                        'disabled' => true,
                    )
                );
        }
        $formMapper
            ->add(
                'damage',
                ModelType::class,
                array(
                    'label' => 'admin.bladedamage.damage',
                    'btn_add' => false,
                    'btn_delete' => false,
                    'required' => true,
                    'query' => $this->dr->findAllEnabledSortedByCodeQ(),
                    'choices_as_values' => true,
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
                    'sonata_help' => 'm',
                )
            )
            ->add(
                'distance',
                null,
                array(
                    'label' => 'admin.bladedamage.distance',
                    'required' => true,
                    'sonata_help' => 'cm',
                )
            )
            ->add(
                'edge',
                ChoiceType::class,
                array(
                    'label' => 'admin.bladedamage.edge',
                    'choices' => BladeDamageEdgeEnum::getEnumArray(),
                    'multiple' => false,
                    'expanded' => false,
                    'required' => true,
                )
            )
            ->add(
                'size',
                null,
                array(
                    'label' => 'admin.bladedamage.size',
                    'required' => true,
                    'sonata_help' => 'cm',
                )
            )
            ->add(
                'damageCategory',
                ModelType::class,
                array(
                    'label' => 'admin.bladedamage.damagecategory',
                    'btn_add' => false,
                    'btn_delete' => false,
                    'required' => true,
                    'query' => $this->dcr->findEnabledSortedByCategoryQ(),
                    'choices_as_values' => true,
                )
            )
            ->add(
                'auditWindmillBlade',
                null,
                array(
                    'label' => 'admin.auditwindmillblade.windmillblade',
                    'required' => true,
                    'disabled' => false,
                    'attr' => array(
                        'hidden' => true,
                    ),
                )
            )
            ->end();
        if ($this->id($this->getSubject()) && $this->getRootCode() != $this->getCode()) {
            // is edit mode, disable on new subjects and is children
            $formMapper
                ->add(
                    'fakeAction',
                    ActionButtonFormType::class,
                    array(
                        'text' => 'Subir fotos',
                        'url' => $this->generateObjectUrl('edit', $this->getSubject()),
                        'label' => 'admin.auditwindmillblade.actions',
                        'mapped' => false,
                        'required' => false,
                    )
                )
                ->end();
        } elseif ($this->id($this->getSubject())) {
            // is edit mode, disable on new subjects and is children
            $formMapper
                ->with('admin.auditwindmillblade.photos', $this->getFormMdSuccessBoxArray(9))
                ->add(
                    'photos',
                    CollectionType::class,
                    array(
                        'label' => ' ',
                        'required' => false,
                        'cascade_validation' => true,
                    ),
                    array(
                        'edit' => 'inline',
                        'inline' => 'table',
                    )
                )
                ->end();
        }

        $this->setTemplate('edit', '::Admin/BladeDamage/edit.html.twig');
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
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
            ->add(
                'status',
                null,
                array(
                    'label' => 'admin.audit.status',
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
                'damage.code',
                null,
                array(
                    'label' => 'admin.audit.status',
                )
            )
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        unset($this->listModes['mosaic']);
        $listMapper
            ->add(
                'status',
                null,
                array(
                    'label' => 'admin.audit.status',
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
                        'edit' => array('template' => '::Admin/Buttons/list__action_edit_button.html.twig'),
                    ),
                )
            );
    }
}
