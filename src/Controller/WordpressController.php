<?php

namespace Sword\SwordBundle\Controller;

use Sword\SwordBundle\Loader\WordpressLoader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class WordpressController extends AbstractController
{
    #[Route('/{path<.*>}', name: Routes::WORDPRESS, priority: -100)]
    public function index(WordpressLoader $wordpressLoader, string $path): Response
    {
        return $wordpressLoader->createWordpressResponse($path);
    }
}
