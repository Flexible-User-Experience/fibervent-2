<?php

namespace App\Factory;

use App\Entity\DeliveryNote;
use App\Entity\WorkerTimesheet;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use CalendarBundle\Entity\Event;

/**
 * Class CalendarEventTrasnformerHelper.
 *
 * @category Service
 */
class CalendarEventTrasnformerHelper
{
    /**
     * @var RouterInterface
     */
    private RouterInterface $router;

    /**
     * Methods.
     */

    /**
     * CalendarEventTrasnformerHelper constructor.
     *
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param DeliveryNote         $deliveryNote
     * @param WorkerTimesheet|null $workerTimesheet
     *
     * @return Event
     */
    public function build(DeliveryNote $deliveryNote, ?WorkerTimesheet $workerTimesheet)
    {
        if (!is_null($workerTimesheet)) {
            $options = [
                'textColor' => '#FFFFFF',
                'borderColor' => '#64B660',
                'backgroundColor' => '#64B660',
                'url' => $this->router->generate('admin_app_workertimesheet_edit', array('id' => $workerTimesheet->getId()), UrlGeneratorInterface::ABSOLUTE_PATH),
            ];
        } else {
            $options = [
                'textColor' => '#FFFFFF',
                'borderColor' => '#EEAC56',
                'backgroundColor' => '#EEAC56',
                'url' => $this->router->generate('admin_app_workertimesheet_create', array('for_delivery_note' => $deliveryNote->getId()), UrlGeneratorInterface::ABSOLUTE_PATH),
            ];
        }
        $eventFullCalendar = new Event('#'.$deliveryNote->getId().' · '.$deliveryNote->getWorkOrder()->getProjectNumber(), $deliveryNote->getDate());
        $eventFullCalendar->setEnd($deliveryNote->getDate());
        $eventFullCalendar->setOptions($options);
        $eventFullCalendar->setAllDay(true);

        return $eventFullCalendar;
    }
}
