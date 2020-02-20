<?php

namespace App\EventListener;

use App\Entity\DeliveryNote;
use App\Entity\User;
use App\Enum\UserRolesEnum;
use App\Factory\CalendarEventTrasnformerHelper;
use App\Repository\DeliveryNoteRepository;
use CalendarBundle\CalendarEvents;
use CalendarBundle\Event\CalendarEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class FullCalendarListener.
 *
 * @category Listener
 */
class FullCalendarListener implements EventSubscriberInterface
{
    /**
     * @var CalendarEventTrasnformerHelper
     */
    private CalendarEventTrasnformerHelper $ceths;

    /**
     * @var DeliveryNoteRepository
     */
    private DeliveryNoteRepository $dnrs;

    /**
     * @var RequestStack
     */
    private RequestStack $rss;

    /**
     * @var RouterInterface
     */
    private RouterInterface $router;

    /**
     * @var AuthorizationCheckerInterface
     */
    private AuthorizationCheckerInterface $acs;

    /**
     * @var TokenStorageInterface
     */
    private TokenStorageInterface $tss;

    /**
     * Methods.
     */

    /**
     * FullcalendarListener constructor.
     *
     * @param CalendarEventTrasnformerHelper $ceths
     * @param DeliveryNoteRepository         $dnrs
     * @param RequestStack                   $rss
     * @param RouterInterface                $router
     * @param AuthorizationCheckerInterface  $acs
     * @param TokenStorageInterface          $tss
     */
    public function __construct(CalendarEventTrasnformerHelper $ceths, DeliveryNoteRepository $dnrs, RequestStack $rss, RouterInterface $router, AuthorizationCheckerInterface $acs, TokenStorageInterface $tss)
    {
        $this->ceths = $ceths;
        $this->dnrs = $dnrs;
        $this->rss = $rss;
        $this->router = $router;
        $this->acs = $acs;
        $this->tss = $tss;
    }

    /**
     * @param CalendarEvent $calendarEvent
     */
    public function onCalendarSetData(CalendarEvent $calendarEvent)
    {
        $startDate = $calendarEvent->getStart();
        $endDate = $calendarEvent->getEnd();

        $referer = $this->rss->getCurrentRequest()->headers->get('referer');
        if ($this->rss->getCurrentRequest()->getBaseUrl()) {
            // probably dev environment
            $path = substr($referer, strpos($referer, $this->rss->getCurrentRequest()->getBaseUrl()));
            $path = str_replace($this->rss->getCurrentRequest()->getBaseUrl(), '', $path);
        } else {
            // prod environment
            $path = str_replace($this->rss->getCurrentRequest()->getSchemeAndHttpHost(), '', $referer);
        }
        /** @var UrlMatcherInterface $matcher */
        $matcher = $this->router->getMatcher();
        $parameters = $matcher->match($path);
        $route = $parameters['_route'];


        if ('sonata_admin_dashboard' == $route && ($this->acs->isGranted(UserRolesEnum::ROLE_OPERATOR) || $this->acs->isGranted(UserRolesEnum::ROLE_TECHNICIAN))) {
            /** @var User $user */
            $user = $this->tss->getToken()->getUser();
            //// admin dashboard action
            $deliveryNotes = $this->dnrs->getItemsByDatesIntervalAndWorkerSortedByDateAsc($startDate, $endDate, $user);
            /** @var DeliveryNote $deliveryNote */
            foreach ($deliveryNotes as $deliveryNote) {
                $calendarEvent->addEvent($this->ceths->build($deliveryNote, $route));
            }
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            CalendarEvents::SET_DATA => 'onCalendarSetData',
        ];
    }
}