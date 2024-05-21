<?php

namespace Sword\SwordBundle\Security;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sword\SwordBundle\Controller\Routes;
use Sword\SwordBundle\Event\WooCommerceRegistrationEvent;
use Sword\SwordBundle\Service\WordpressService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use WC_Session_Handler;
use Williarin\WordpressInterop\Bridge\Entity\Option;
use Williarin\WordpressInterop\Bridge\Entity\Page;
use Williarin\WordpressInterop\EntityManagerInterface;
use Williarin\WordpressInterop\Exception\OptionNotFoundException;
use WP_User;

class UserAuthenticator extends AbstractLoginFormAuthenticator implements WordpressService
{
    private ?string $wordpressUsername = null;
    private ?string $wordpressPassword = null;
    private bool $wordpressRememberMe = false;
    private ?string $csrfToken = null;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RequestStack $requestStack,
        private readonly TokenStorageInterface $tokenStorage,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function getPriority(): int
    {
        return 0;
    }

    public function initialize(): void
    {
        add_action('wp_login', [$this, 'onWordpressLogin'], 100, 2);
        add_action('woocommerce_login_failed', [$this, 'onWooCommerceLoginTerminate'], -100);
        add_action('wp_logout', [$this, 'onWordpressLogout'], -100);
        add_action('woocommerce_created_customer', [$this, 'onWooCommerceRegister'], -100, 3);

        add_filter('woocommerce_registration_auth_new_customer', '__return_false');
    }

    public function onWordpressLogin(string $username, WP_User $wordpressUser): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $this->wordpressUsername = $username;
        $this->wordpressPassword = $request?->get('password', $request?->get('pwd', ''));
        $this->wordpressRememberMe = (bool) $request?->get('rememberme');

        $this->onWordpressLoginSuccess();
    }

    public function onWooCommerceLoginTerminate(): void
    {
        if (!$this->wordpressUsername || !$this->wordpressPassword) {
            return;
        }

        $this->onWordpressLoginSuccess();
    }

    public function onWordpressLogout(): void
    {
        if (!$this->authorizationChecker->isGranted('ROLE_USER')) {
            return;
        }

        if (class_exists('WC_Session_Handler')) {
            $woocommerceSession = apply_filters('woocommerce_session_handler', 'WC_Session_Handler');

            if ($woocommerceSession instanceof WC_Session_Handler) {
                $woocommerceSession->destroy_session();
            }
        }

        $logoutEvent = new LogoutEvent($this->requestStack->getCurrentRequest(), $this->tokenStorage->getToken());
        $this->eventDispatcher->dispatch($logoutEvent);

        $response = $logoutEvent->getResponse();
        if (!$response instanceof Response) {
            $response = new RedirectResponse($this->urlGenerator->generate(Routes::WORDPRESS, [
                'path' => ''
            ]));
        }

        $this->tokenStorage->setToken();
        
        $session = $this->requestStack->getSession();
        
        $session->invalidate();
        $session->set('logoutSuccessResponse', $response);
    }

    public function onWooCommerceRegister(int $customerId, array $newCustomerData, bool $passwordGenerated): void
    {
        $this->eventDispatcher->dispatch(new WooCommerceRegistrationEvent($customerId, $newCustomerData));
    }

    public function supports(Request $request): bool
    {
        $data = $this->requestStack->getSession()
            ->getFlashBag()
            ->get('wp_login');

        if (!empty($data)) {
            [$this->wordpressUsername, $this->wordpressPassword, $this->wordpressRememberMe, $this->csrfToken] = $data;
        }

        return $this->wordpressUsername && $this->wordpressPassword && \in_array(
                $request->getPathInfo(),
                [$this->getLoginUrl($request), '/wp-login.php'],
                true,
            );
    }

    public function start(Request $request, AuthenticationException $authException = null): RedirectResponse
    {
        $url = '/wp-login.php?' . http_build_query([
                'redirect_to' => $request->getUri(),
                'reauth' => 0,
            ]);
        
        return new RedirectResponse($url, 302);
    }

    public function getLoginUrl(Request $request): string
    {
        try {
            $myAccountId = (int) $this->entityManager->getRepository(Option::class)
                ->find('woocommerce_myaccount_page_id');

            return sprintf(
                '/%s/',
                $this->entityManager->getRepository(Page::class)
                    ->find($myAccountId)
                    ->postName,
            );
        } catch (OptionNotFoundException) {
            return '/wp-login.php';
        }
    }

    public function authenticate(Request $request): Passport
    {
        $badges = [new CsrfTokenBadge('authenticate', $this->csrfToken)];

        if ($this->wordpressRememberMe) {
            $badges[] = new RememberMeBadge();
        }

        return new Passport(
            new UserBadge($this->wordpressUsername),
            new PasswordCredentials($this->wordpressPassword),
            $badges,
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $redirectPath = $this->getRedirectPath($request);

        if ($request->getPathInfo() === '/wp-login.php') {
            if ($redirectPath) {
                return new RedirectResponse($redirectPath);
            }

            return new RedirectResponse($this->urlGenerator->generate(Routes::WORDPRESS, [
                'path' => ''
            ]));
        }

        return $redirectPath
            ? new RedirectResponse($redirectPath)
            : null;
    }

    private function getRedirectPath(Request $request): ?string
    {
        $referer = $request->headers->get('referer');

        if ($referer && ($queryString = parse_url($referer, PHP_URL_QUERY))) {
            parse_str($queryString, $result);

            if (!empty($result['redirect_to'])) {
                return $result['redirect_to'];
            }
        }

        return null;
    }
    
    private function onWordpressLoginSuccess(): void
    {
        $session = $this->requestStack->getSession();
        
        $session->set('loginSuccessData', [
            'username' => $this->wordpressUsername,
            'password' => $this->wordpressPassword,
            'rememberMe' => $this->wordpressRememberMe,
        ]);
    }
}
