<?php

namespace App\Admin;

use App\Entity\Customer;
use App\Enum\UserRolesEnum;
use App\Enum\WindfarmLanguageEnum;
use Sonata\UserBundle\Admin\Model\UserAdmin as ParentUserAdmin;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class UserAdmin.
 *
 * @category Admin
 */
class UserAdmin extends ParentUserAdmin
{
    /**
     * @var array
     */
    protected $perPageOptions = array(25, 50, 100, 200);

    /**
     * @var int
     */
    protected $maxPerPage = 25;

    protected $classnameLabel = 'admin.user.title';
    protected $baseRoutePattern = 'customers/user';
    protected $datagridValues = array(
        '_sort_by' => 'lastname',
        '_sort_order' => 'asc',
    );

    /**
     * Available routes.
     *
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection
            ->add('profile')
            ->add('buildPresenceMonitoring', $this->getRouterIdParameter().'/build-presence-monitoring')
            ->add('buildWorkerTimesheet', $this->getRouterIdParameter().'/build-worker-timesheet')
            ->remove('show')
            ->remove('batch')
            ->remove('delete')
            ->remove('export')
        ;
    }

    /**
     * Remove batch action list view first column.
     *
     * @return array
     */
    public function getBatchActions()
    {
        $actions = parent::getBatchActions();
        unset($actions['delete']);

        return $actions;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper): void
    {
        /* @var object $formMapper */
        $formMapper
            ->with('admin.common.general', array('class' => 'col-md-6'))
            ->add(
                'imageFile',
                FileType::class,
                array(
                    'label' => 'admin.bladephoto.imagefile',
                    'help' => $this->getImageHelperFormMapperWithThumbnail(),
                    'required' => false,
                )
            )
            ->add(
                'username',
                null,
                array(
                    'label' => 'admin.user.username',
                    'help' => 'admin.user.username_help',
                )
            )
            ->add(
                'email',
                EmailType::class,
                array(
                    'label' => 'admin.user.email',
                )
            )
            ->add(
                'plainPassword',
                PasswordType::class,
                array(
                    'label' => 'admin.user.password',
                    'required' => (!$this->getSubject() || is_null($this->getSubject()->getId())),
                )
            )
            ->add(
                'firstname',
                null,
                array(
                    'label' => 'admin.user.firstname',
                    'required' => false,
                )
            )
            ->add(
                'lastname',
                null,
                array(
                    'label' => 'admin.user.lastname',
                    'required' => false,
                )
            )
            ->add(
                'phone',
                null,
                array(
                    'label' => 'admin.user.phone',
                )
            )
            ->end()
            ->with('admin.user.roles', array('class' => 'col-md-3'))
            ->add(
                'roles',
                ChoiceType::class,
                array(
                    'label' => 'admin.user.rol',
                    'choices' => UserRolesEnum::getEnumArray(),
                    'multiple' => true,
                    'expanded' => true,
                    'required' => false,
                )
            )
            ->end()
            ->with('admin.common.controls', array('class' => 'col-md-3'))
            ->add(
                'customer',
                EntityType::class,
                array(
                    'label' => 'admin.customer.title',
                    'required' => false,
                    'class' => Customer::class,
                    'query_builder' => $this->getConfigurationPool()->getContainer()->get('app.customer_repository')->findAllSortedByNameQB(),
                )
            )
            ->add(
                'nif',
                TextType::class,
                array(
                    'label' => 'admin.presencemonitoring.operator_nif',
                    'required' => false,
                )
            )
            ->add(
                'language',
                ChoiceType::class,
                array(
                    'label' => 'admin.user.language',
                    'choices' => WindfarmLanguageEnum::getEnumArrayString(),
                    'multiple' => false,
                    'expanded' => false,
                    'required' => true,
                )
            )
            ->add(
                'enabled',
                CheckboxType::class,
                array(
                    'label' => 'admin.common.enabled',
                    'required' => false,
                )
            )
            ->end();
    }

    /**
     * @param DatagridMapper $filterMapper
     */
    protected function configureDatagridFilters(DatagridMapper $filterMapper): void
    {
        $filterMapper
            ->add(
                'username',
                null,
                array(
                    'label' => 'admin.user.username',
                )
            )
            ->add(
                'firstname',
                null,
                array(
                    'label' => 'admin.user.firstname',
                )
            )
            ->add(
                'lastname',
                null,
                array(
                    'label' => 'admin.user.lastname',
                )
            )
            ->add(
                'email',
                null,
                array(
                    'label' => 'admin.user.email',
                )
            )
            ->add(
                'phone',
                null,
                array(
                    'label' => 'admin.user.phone',
                )
            )
            ->add(
                'nif',
                null,
                array(
                    'label' => 'admin.presencemonitoring.operator_nif',
                )
            )
            ->add(
                'roles',
                'doctrine_orm_choice',
                array(
                    'label' => 'admin.user.roles',
                    'field_type' => ChoiceType::class,
                    'field_options' => array(
                        'choices' => UserRolesEnum::getReversedEnumArray(),
                    ),
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
    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add(
                'image',
                null,
                array(
                    'label' => 'admin.bladephoto.imagefile',
                    'template' => 'Admin/Cells/list__cell_image_field.html.twig',
                )
            )
            ->add(
                'firstname',
                null,
                array(
                    'label' => 'admin.user.firstname',
                    'editable' => true,
                )
            )
            ->add(
                'lastname',
                null,
                array(
                    'label' => 'admin.user.lastname',
                    'editable' => true,
                )
            )
            ->add(
                'email',
                null,
                array(
                    'label' => 'admin.user.email',
                    'editable' => true,
                )
            )
            ->add(
                'phone',
                null,
                array(
                    'label' => 'admin.user.phone',
                    'editable' => true,
                )
            )
            ->add(
                'roles',
                null,
                array(
                    'label' => 'admin.user.roles',
                    'template' => 'Admin/Cells/list__cell_user_roles.html.twig',
                    'header_class' => 'text-center',
                    'row_align' => 'center',
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
                        'buildPresenceMonitoring' => array('template' => 'Admin/Buttons/list__action_build_presence_monitoring_button.html.twig'),
                        'buildWorkerTimesheet' => array('template' => 'Admin/Buttons/list__action_worker_timesheet_pdf_monthly_resume_button.html.twig'),
                    ),
                )
            );
    }

    /**
     * Get image helper form mapper with thumbnail.
     *
     * @param int $minWidth
     *
     * @return string
     */
    protected function getImageHelperFormMapperWithThumbnail($minWidth = 1200)
    {
        $lis = $this->getConfigurationPool()->getContainer()->get('liip_imagine.cache.manager');
        $vus = $this->getConfigurationPool()->getContainer()->get('app.smart_assets_helper')->getVichUploaderHerlper();

        return ($this->getSubject() ? $this->getSubject()->getImageName() ? '<img src="'.$lis->getBrowserPath(
                $vus->asset($this->getSubject(), 'imageFile'),
                '480xY'
            ).'" class="admin-preview img-responsive" alt="thumbnail"/>' : '' : '').'<span style="width:100%;display:block;">'.$this->trans('admin.photo.help', ['%width%' => 320]).'</span>';
    }
}
