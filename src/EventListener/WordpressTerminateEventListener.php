<?php

declare(strict_types=1);

namespace Sword\SwordBundle\EventListener;

use Sword\SwordBundle\Controller\Routes;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class WordpressTerminateEventListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly EventDispatcherInterface $dispatcher
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => ['onResponse', -2048],
        ];
    }

    public function onResponse(ResponseEvent $event): void
    {
        if ($event->getRequest()->attributes->get('_route') === Routes::WORDPRESS) {
            $response = $event->getResponse();

            $response->sendHeaders();
            $response->sendContent();

            $this->dispatcher->dispatch(
                new TerminateEvent($event->getKernel(), $event->getRequest(), $response),
                KernelEvents::TERMINATE,
            );

            Response::closeOutputBuffers(0, true);

            // Trigger WordPress register_shutdown_function() callbacks
            exit;
        }
    }
}
