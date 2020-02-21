<?php

namespace App\Admin\Block;

use App\Repository\WorkOrderRepository;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Templating\EngineInterface;

/**
 * Class WorkOrdersNotFinishedBlock.
 *
 * @category Block
 */
class WorkOrdersNotFinishedBlock extends AbstractBlockService
{
    /**
     * @var WorkOrderRepository
     */
    private WorkOrderRepository $wors;

    /**
     * Methods
     */

    /**
     * Constructor.
     *
     * @param string              $name
     * @param EngineInterface     $templating
     * @param WorkOrderRepository $wors
     */
    public function __construct($name, EngineInterface $templating, WorkOrderRepository $wors)
    {
        parent::__construct($name, $templating);
        $this->wors = $wors;
    }

    /**
     * Execute.
     *
     * @param BlockContextInterface $blockContext
     * @param Response|null         $response
     *
     * @return Response
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $notFinishedWorkOrders = $this->wors->findAvailableSortedByProjectNumber(null, 'DESC');

        return $this->renderResponse(
            $blockContext->getTemplate(),
            array(
                'block' => $blockContext->getBlock(),
                'settings' => $blockContext->getSettings(),
                'title' => 'WorkOrdersNotFinishedBlock',
                'not_finished_work_orders' => $notFinishedWorkOrders,
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
        return 'event_calendar';
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'title' => 'WorkOrdersNotFinishedBlock',
            'content' => 'Default content',
            'template' => 'Admin/Blocks/work_orders_not_finished.html.twig',
        ));
    }
}
