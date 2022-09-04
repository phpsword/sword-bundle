<?php

declare(strict_types=1);

namespace Sword\SwordBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class WooCommerceRegistrationEvent extends Event
{
    public function __construct(
        public readonly int $customerId,
        public readonly array $customerData,
    ) {
    }
}
