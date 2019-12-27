<?php

namespace App\Admin\Block;

use App\Enum\AuditStatusEnum;
use App\Service\AuthCustomerService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Templating\EngineInterface;
use Twig\Environment;

/**
 * Class AuditsBlock.
 *
 * @category Block
 */
class AuditsBlock extends AbstractBlockService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var AuthCustomerService
     */
    private $acs;

    /**
     * Methods.
     */

    /**
     * Constructor.
     *
     * @param string                 $name
     * @param EngineInterface        $templating
     * @param EntityManagerInterface $em
     * @param AuthCustomerService    $acs
     */
    public function __construct($name, EngineInterface $templating, EntityManagerInterface $em, AuthCustomerService $acs)
    {
        parent::__construct($name, $templating);
        $this->em = $em;
        $this->acs = $acs;
    }

    /**
     * Execute.
     *
     * @param BlockContextInterface $blockContext
     * @param Response              $response
     *
     * @return Response
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        if ($this->acs->isCustomerUser()) {
            $doingAudits = $this->em->getRepository('App:Audit')->getDoingAuditsByCustomerAmount($this->acs->getCustomer());
            $pendingAudits = $this->em->getRepository('App:Audit')->getPendingAuditsByCustomerAmount($this->acs->getCustomer());
        } else {
            $doingAudits = $this->em->getRepository('App:Audit')->getDoingAuditsAmount();
            $pendingAudits = $this->em->getRepository('App:Audit')->getPendingAuditsAmount();
        }

        return $this->renderResponse(
            $blockContext->getTemplate(),
            array(
                'block' => $blockContext->getBlock(),
                'settings' => $blockContext->getSettings(),
                'title' => 'Estat Auditories',
                'doing_audits' => $doingAudits,
                'pending_audits' => $pendingAudits,
                'status_pending' => AuditStatusEnum::PENDING,
                'status_doing' => AuditStatusEnum::DOING,
            ),
            $response
        );
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return 'done_audits';
    }

    /**
     * Define the default options for the block.
     *
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'title' => 'Resume',
                'content' => 'Default content',
                'template' => 'Admin/Blocks/block_audits.html.twig',
            )
        );
    }
}
