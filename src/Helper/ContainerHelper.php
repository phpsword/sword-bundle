<?php

declare(strict_types=1);

namespace Sword\SwordBundle\Helper;

use Sword\SwordBundle\Loader\WordpressLoader;
use Sword\SwordBundle\Service\WordpressService;

/**
 * @template T
 * @param class-string<T> $serviceId
 * @return T
 */
function get_symfony_service(string $serviceId): mixed
{
    /** @var WordpressLoader $wordpressLoader */
    global $wordpressLoader;

    return $wordpressLoader->container->get($serviceId);
}

function get_symfony_parameter(string $parameter): mixed
{
    /** @var WordpressLoader $wordpressLoader */
    global $wordpressLoader;

    return $wordpressLoader->container->getParameter($parameter);
}

function initialize_services(): void
{
    /** @var WordpressLoader $wordpressLoader */
    global $wordpressLoader;

    $services = iterator_to_array($wordpressLoader->wordpressServices->getIterator());
    $wordpressLoader->lazyServiceInstantiator->requireAll();

    usort(
        $services,
        static fn (WordpressService $a, WordpressService $b) => $b->getPriority() <=> $a->getPriority(),
    );

    foreach ($services as $service) {
        $service->initialize();
    }
}
