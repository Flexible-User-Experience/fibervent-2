<?php

namespace App\EventListener;

use App\Entity\User;
use App\Enum\WindfarmLanguageEnum;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * Class UserLoginLocaleListener.
 *
 * @category Listener
 *
 * Stores the locale of the user in the session after the
 * login. This can be used by the LocaleListener afterwards.
 */
class UserLoginLocaleListener
{
    /**
     * @var Session
     */
    private $session;

    /**
     * Methods.
     */

    /**
     * UserLoginLocaleListener constructor.
     *
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @param InteractiveLoginEvent $event
     */
    public function onInteractiveLogin(InteractiveLoginEvent $event)
    {
        /** @var User $user */
        $user = $event->getAuthenticationToken()->getUser();

        if (null !== $user->getLanguage()) {
            $this->session->set('_locale', WindfarmLanguageEnum::getEnumArray()[$user->getLanguage()]);
        }
    }
}
