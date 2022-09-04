<?php

declare(strict_types=1);

namespace Sword\SwordBundle\Loader;

use Sword\SwordBundle\Event\WooCommerceRegistrationEvent;
use Sword\SwordBundle\Exception\WordpressLoginSuccessfulException;
use Sword\SwordBundle\Exception\WordpressLougoutSuccessfulException;
use Sword\SwordBundle\Security\UserAuthenticator;
use Sword\SwordBundle\Store\WordpressWidgetStore;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final class WordpressLoader implements EventSubscriberInterface
{
    private ?array $auth = null;

    public function __construct(
        #[Autowire('@service_container')] public readonly Container $container,
        #[TaggedIterator('sword.wordpress_service')] public readonly iterable $wordpressServices,
        public readonly WordpressWidgetStore $widgetStore,
        public readonly LazyServiceInstantiator $lazyServiceInstantiator,
        private readonly RequestStack $requestStack,
        private readonly UserAuthenticator $userAuthenticator,
        private readonly CsrfTokenManagerInterface $csrfTokenManager,
        #[Autowire('%sword.wordpress_core_dir%')] private readonly string $wordpressDirectory,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            WooCommerceRegistrationEvent::class => 'onWooCommerceRegistration',
        ];
    }

    public function onWooCommerceRegistration(WooCommerceRegistrationEvent $event): void
    {
        $this->auth = [
            'id' => $event->customerId,
            'data' => $event->customerData,
        ];
    }

    public function createWordpressResponse(string $urlPathName): Response
    {
        $request = $this->requestStack->getCurrentRequest();

        ob_start();
        $obLevel = ob_get_level();

        global $wordpressLoader;
        $wordpressLoader = $this;

        foreach (WordpressGlobals::GLOBALS as $global) {
            global $$global;
        }

        $entryPoint = $this->wordpressDirectory . '/index.php';

        if (str_starts_with($urlPathName, 'wp-login.php')) {
            $_SERVER['PHP_SELF'] = '/' . basename($urlPathName);
            $entryPoint = $this->wordpressDirectory . '/wp-login.php';
        } elseif (str_starts_with($urlPathName, 'wp-admin/')) {
            if ($urlPathName === 'wp-admin/') {
                $urlPathName = 'wp-admin/index.php';
            }

            $_SERVER['PHP_SELF'] = '/' . $urlPathName;
            $entryPoint = $this->wordpressDirectory . '/' . $urlPathName;
        } else {
            $_SERVER['PHP_SELF'] = '/' . $urlPathName;
        }

        try {
            require_once $entryPoint;
        } catch (WordpressLoginSuccessfulException $exception) {
            return $this->getAuthResponse($exception->username, $exception->password, $exception->rememberMe);
        } catch (WordpressLougoutSuccessfulException) {
            return new RedirectResponse($this->userAuthenticator->getLoginUrl($request));
        }

        if ($this->auth) {
            wc_set_customer_auth_cookie($this->auth['id']);

            return $this->getAuthResponse(
                $this->auth['data']['user_login'] ?? '',
                $this->auth['data']['user_pass'] ?? '',
                false,
            );
        }

        while (ob_get_level() > $obLevel) {
            ob_end_flush();
        }

        return new Response(ob_get_clean(), is_404() ? 404 : 200);
    }

    private function getAuthResponse(string $username, string $password, bool $rememberMe): RedirectResponse
    {
        $session = $this->requestStack->getSession();
        $session->getFlashBag()
            ->set(
                'wp_login',
                [$username, $password, $rememberMe, $this->csrfTokenManager->getToken('authenticate') ->getValue()],
            );

        return new RedirectResponse($this->requestStack->getCurrentRequest()?->getRequestUri(), 302);
    }
}
