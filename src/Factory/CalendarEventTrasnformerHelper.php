<?php

namespace App\Factory;

use App\Entity\DeliveryNote;
use App\Entity\User;
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
     * @param DeliveryNote $deliveryNote
     * @param User         $worker
     *
     * @return Event
     */
    public function build(DeliveryNote $deliveryNote, User $worker)
    {
//        if ($appEvent->getGroup()->isForPrivateLessons()) {
//            $eventFullCalendar = new Event($appEvent->getShortCalendarTitleForPrivateLessons(), $appEvent->getBegin());
//        } else {
//            $eventFullCalendar = new Event($appEvent->getShortCalendarTitle(), $appEvent->getBegin());
//        }
        $eventFullCalendar = new Event($deliveryNote->getId().' · '.$deliveryNote->getWorkOrder()->getProjectNumber(), $deliveryNote->getDate());
        $eventFullCalendar->setEnd($deliveryNote->getDate());
        $eventFullCalendar->setOptions([
            'textColor' => '#FFFFFF',
            'borderColor' => '#00FF00',
            'backgroundColor' => '#00FF00',
//            'url' => $this->router->generate('admin_app_event_edit', array('id' => $appEvent->getId()), UrlGeneratorInterface::ABSOLUTE_PATH),
        ]);
        $eventFullCalendar->setAllDay(true);

        return $eventFullCalendar;
    }
}
