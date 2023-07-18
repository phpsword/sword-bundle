<?php

declare(strict_types=1);

namespace Sword\SwordBundle\Controller;

use Sword\SwordBundle\Loader\WordpressLoader;
use Sword\SwordBundle\Security\UserReauthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
final class ReauthenticateController extends AbstractController
{
    #[Route('/reauthenticate', name: Routes::REAUTHENTICATE, priority: 100)]
    public function reauthenticate(
        Request $request,
        WordpressLoader $wordpressLoader,
        UserReauthenticator $userReauthenticator,
    ): Response {
        $response = $request->headers->get('referer') === $request->getRequestUri()
            ? $this->redirectToRoute(Routes::WORDPRESS, [
                'path' => ''
            ])
            : $this->redirect($request->headers->get('referer') ?: $this->generateUrl(Routes::WORDPRESS, [
                'path' => ''
            ]));

        $wordpressLoader->loadWordpress();
        $userReauthenticator->reauthenticate();

        return $response;
    }
}
