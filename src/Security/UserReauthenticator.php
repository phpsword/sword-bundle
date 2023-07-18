<?php

declare(strict_types=1);

namespace Sword\SwordBundle\Security;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserCheckerInterface;

final class UserReauthenticator
{
    public function __construct(
        private readonly UserAuthenticator $authenticator,
        private readonly UserCheckerInterface $userChecker,
        private readonly UserProvider $userProvider,
        private readonly RequestStack $requestStack,
        #[Autowire(service: 'security.authenticator.managers_locator')]
        private readonly ServiceLocator $managersLocator,
    ) {
    }

    public function reauthenticate(): bool
    {
        if (!is_user_logged_in()) {
            return false;
        }

        $user = $this->userProvider->loadUserByIdentifier(wp_get_current_user()->user_login);
        $this->userChecker->checkPreAuth($user);
        $this->managersLocator->get('main')
            ->authenticateUser($user, $this->authenticator, $this->requestStack->getMainRequest());

        return true;
    }
}
