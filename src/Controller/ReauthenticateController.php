<?php

declare(strict_types=1);

namespace Sword\SwordBundle\Controller;

use Sword\SwordBundle\Loader\WordpressLoader;
use Sword\SwordBundle\Security\UserAuthenticator;
use Sword\SwordBundle\Security\UserProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserCheckerInterface;

#[AsController]
final class ReauthenticateController extends AbstractController
{
    #[Route('/reauthenticate', name: Routes::REAUTHENTICATE, priority: 100)]
    public function reauthenticate(
        Request $request,
        WordpressLoader $wordpressLoader,
        UserAuthenticator $authenticator,
        UserCheckerInterface $userChecker,
        UserProvider $userProvider,
        #[Autowire(service: 'security.authenticator.managers_locator')]
        ServiceLocator $managersLocator,
    ): Response {
        $response = $request->headers->get('referer') === $request->getRequestUri()
            ? $this->redirectToRoute(Routes::WORDPRESS)
            : $this->redirect($request->headers->get('referer'));

        $wordpressLoader->loadWordpress();

        if (!is_user_logged_in()) {
            return $response;
        }

        $user = $userProvider->loadUserByIdentifier(wp_get_current_user()->user_login);
        $userChecker->checkPreAuth($user);
        $managersLocator->get('main')
            ->authenticateUser($user, $authenticator, $request);

        return $response;
    }
}
