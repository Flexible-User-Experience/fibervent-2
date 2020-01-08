<?php

namespace App\Admin;

use App\Entity\User;
use App\Enum\UserRolesEnum;
use App\Enum\WindfarmLanguageEnum;
use Doctrine\ORM\QueryBuilder;
use Oh\GoogleMapFormTypeBundle\Form\Type\GoogleMapType;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\Form\Type\EqualType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

/**
 * Class WindfarmAdmin.
 *
 * @category Admin
 */
class WindfarmAdmin extends AbstractBaseAdmin
{
    protected $classnameLabel = 'admin.windfarm.title';
    protected $baseRoutePattern = 'windfarms/windfarm';
    protected $datagridValues = array(
        'enabled' => array('type' => EqualType::TYPE_IS_EQUAL, 'value' => true),
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
        $collection
            ->add('show', $this->getRouterIdParameter().'/show')
            ->add('audits', $this->getRouterIdParameter().'/audits')
            ->add('map', $this->getRouterIdParameter().'/map')
            ->add('excel', $this->getRouterIdParameter().'/excel')
            ->add('pdf', $this->getRouterIdParameter().'/pdf')
            ->add('excelAttachment', $this->getRouterIdParameter().'/excel-attachment', array(
                '_format' => 'xls',
            ), array(
                '_format' => 'csv|xls|xlsx',
            ))
            ->add('pdfAttachment', $this->getRouterIdParameter().'/pdf-attachment')
            ->remove('delete');
    }

    /**
     * @param string $context
     *
     * @return QueryBuilder
     */
    public function createQuery($context = 'list')
    {
        /** @var QueryBuilder $query */
        $query = parent::createQuery($context);
        // Customer filter
        if ($this->acs->isGranted(UserRolesEnum::ROLE_CUSTOMER)) {
            /** @var User $user */
            $user = $this->tss->getToken()->getUser();
            $query
                ->andWhere($query->getRootAliases()[0].'.customer = :customer')
                ->setParameter('customer', $user->getCustomer())
            ;
        }

        return $query;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $customer = null;
        if (null !== $this->getSubject()) {
            $customer = $this->getSubject()->getCustomer();
        }
        $formMapper
            ->with('admin.common.general', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'customer',
                ModelType::class,
                array(
                    'label' => 'admin.windfarm.customer',
                    'required' => true,
                    'multiple' => false,
                    'btn_add' => false,
                    'query' => $this->cr->findEnabledSortedByNameQ(),
                )
            )
            ->add(
                'code',
                null,
                array(
                    'label' => 'admin.customer.code',
                    'required' => false,
                )
            )
            ->add(
                'name',
                null,
                array(
                    'label' => 'admin.customer.name',
                )
            )
            ->add(
                'enabled',
                null,
                array(
                    'label' => 'admin.common.enabled',
                )
            )
            ->end()
            ->with('admin.customer.postal_data', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'address',
                null,
                array(
                    'label' => 'admin.customer.address',
                )
            )
            ->add(
                'zip',
                null,
                array(
                    'label' => 'admin.customer.zip',
                )
            )
            ->add(
                'city',
                null,
                array(
                    'label' => 'admin.customer.city',
                )
            )
            ->add(
                'state',
                ModelType::class,
                array(
                    'label' => 'admin.customer.state',
                    'btn_add' => true,
                    'btn_delete' => false,
                    'required' => true,
                    'query' => $this->sr->findAllSortedByNameQ(),
                )
            )
            ->end()
            ->with('admin.common.controls', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'manager',
                ModelType::class,
                array(
                    'label' => 'admin.windfarm.manager',
                    'btn_add' => false,
                    'btn_delete' => false,
                    'required' => false,
                    'property' => 'contactInfoString',
                    'query' => $this->ur->findEnabledSortedByNameQ($customer),
                )
            )
            ->add(
                'year',
                null,
                array(
                    'label' => 'admin.windfarm.year',
                    'required' => false,
                )
            )
            ->add(
                'power',
                null,
                array(
                    'label' => 'admin.turbine.power',
                    'required' => false,
                )
            )
            ->add(
                'windmillAmount',
                null,
                array(
                    'label' => 'admin.windfarm.windmill_amount',
                    'required' => false,
                )
            )
            ->add(
                'language',
                ChoiceType::class,
                array(
                    'label' => 'admin.windfarm.pdf_language',
                    'choices' => WindfarmLanguageEnum::getEnumArrayString(),
                    'multiple' => false,
                    'expanded' => true,
                    'required' => true,
                )
            )
            ->end()
            ->with('admin.windfarm.geoposition', $this->getFormMdSuccessBoxArray(12))
            ->add(
                'latLng',
                GoogleMapType::class,
                array(
                    'label' => 'admin.windfarm.latlng',
                    'addr_type' => HiddenType::class,
                    'required' => false,
                )
            )
            ->end();
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        if ($this->acs->isGranted(UserRolesEnum::ROLE_OPERATOR)) {
            $datagridMapper
                ->add(
                    'customer',
                    null,
                    array(
                        'label' => 'admin.windfarm.customer',
                    )
                )
                ->add(
                    'code',
                    null,
                    array(
                        'label' => 'admin.customer.code',
                    )
                )
            ;
        }
        $datagridMapper
            ->add(
                'name',
                null,
                array(
                    'label' => 'admin.customer.name',
                )
            )
        ;
        if ($this->acs->isGranted(UserRolesEnum::ROLE_OPERATOR)) {
            $datagridMapper
                ->add(
                    'address',
                    null,
                    array(
                        'label' => 'admin.customer.address',
                    )
                )
                ->add(
                    'zip',
                    null,
                    array(
                        'label' => 'admin.customer.zip',
                    )
                )
                ->add(
                    'city',
                    null,
                    array(
                        'label' => 'admin.customer.city',
                    )
                )
            ;
        }
        $datagridMapper
            ->add(
                'state',
                null,
                array(
                    'label' => 'admin.customer.state',
                )
            )
        ;
        if ($this->acs->isGranted(UserRolesEnum::ROLE_OPERATOR)) {
            $datagridMapper
                ->add(
                    'manager',
                    null,
                    array(
                        'label' => 'admin.windfarm.manager',
                    ),
                    EntityType::class,
                    array(
                        'class' => User::class,
                        'query_builder' => $this->ur->findAllSortedByNameQB(),
                    )
                )
            ;
        } else {
            $datagridMapper
                ->add(
                    'manager',
                    null,
                    array(
                        'label' => 'admin.windfarm.manager',
                    ),
                    EntityType::class,
                    array(
                        'class' => User::class,
                        'query_builder' => $this->ur->findRegionalManagersByCustomerQB($this->tss->getToken()->getUser()->getCustomer()),
                    )
                )
            ;
        }
        if ($this->acs->isGranted(UserRolesEnum::ROLE_OPERATOR)) {
            $datagridMapper
                ->add(
                    'power',
                    null,
                    array(
                        'label' => 'admin.turbine.power',
                    )
                )
            ;
        }
        $datagridMapper
            ->add(
                'year',
                null,
                array(
                    'label' => 'admin.windfarm.year',
                )
            )
            ->add(
                'enabled',
                null,
                array(
                    'label' => 'admin.common.enabled',
                )
            );
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        if ($this->acs->isGranted(UserRolesEnum::ROLE_OPERATOR)) {
            $listMapper
                ->add(
                    'customer',
                    null,
                    array(
                        'label' => 'admin.windfarm.customer',
                        'sortable' => true,
                        'sort_field_mapping' => array('fieldName' => 'name'),
                        'sort_parent_association_mappings' => array(array('fieldName' => 'customer')),
                    )
                )
            ;
        }
        $listMapper
            ->add(
                'name',
                null,
                array(
                    'label' => 'admin.customer.name',
                    'editable' => true,
                )
            )
            ->add(
                'city',
                null,
                array(
                    'label' => 'admin.customer.city',
                    'editable' => true,
                )
            )
            ->add(
                'manager',
                null,
                array(
                    'label' => 'admin.windfarm.manager',
                    'sortable' => true,
                    'sort_field_mapping' => array('fieldName' => 'lastname'),
                    'sort_parent_association_mappings' => array(array('fieldName' => 'manager')),
                    'template' => 'Admin/Cells/list__windfarm_manager_fullname.html.twig',
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
                        'show' => array('template' => 'Admin/Buttons/list__action_show_button.html.twig'),
                        'audits' => array('template' => 'Admin/Buttons/list__action_audits_button.html.twig'),
                        'excel' => array('template' => 'Admin/Buttons/list__action_excel_button.html.twig'),
                        'pdf' => array('template' => 'Admin/Buttons/list__action_pdf_windfarm_button.html.twig'),
                        'map' => array('template' => 'Admin/Buttons/list__action_map_button.html.twig'),
                    ),
                )
            );
    }
}
