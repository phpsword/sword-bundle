<?php

declare(strict_types=1);

namespace Sword\SwordBundle\EventListener;

use Sword\SwordBundle\Controller\Routes;
use Sword\SwordBundle\Loader\WordpressLoader;
use Sword\SwordBundle\Security\UserReauthenticator;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class WordpressLoggedInStatusCheckEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        private readonly WordpressLoader $wordpressLoader,
        private readonly UserReauthenticator $userReauthenticator,
        #[Autowire('%sword.app_namespace%')]
        private readonly string $appNamespace,
        #[Autowire('%sword.wordpress_host%')]
        private readonly ?string $wordpressHost,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'checkWordpressLoggedInStatus',
        ];
    }

    public function checkWordpressLoggedInStatus(RequestEvent $event): void
    {
        $controller = $event->getRequest()
            ->attributes->get('_controller');

        if (
            !(str_starts_with($controller, 'Sword\\SwordBundle\\') || str_starts_with($controller, $this->appNamespace))
            || $event->getRequest()
                ->attributes->get('_route') === Routes::REAUTHENTICATE
            || $this->tokenStorage->getToken()?->getUser()
        ) {
            return;
        }

        $host = $this->wordpressHost ?? $event->getRequest()
            ->getSchemeAndHttpHost();
        $cookieName = 'wordpress_logged_in_' . md5($host);

        if (\array_key_exists($cookieName, $event->getRequest()->cookies->all())) {
            $this->wordpressLoader->loadWordpress();

            if (!is_user_logged_in()) {
                $response = new RedirectResponse($event->getRequest()->getRequestUri());
                $response->headers->clearCookie($cookieName);
                $event->setResponse($response);

                return;
            }

            $this->userReauthenticator->reauthenticate();

            if (str_starts_with($controller, 'Sword\\SwordBundle\\')) {
                $event->setResponse(new RedirectResponse($host . $event->getRequest()->getRequestUri()));
            }
        }
    }
}
