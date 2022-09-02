<?php

declare(strict_types=1);

namespace Sword\SwordBundle\Service;

abstract class AbstractWordpressService implements WordpressService
{
    public function getPriority(): int
    {
        return 0;
    }
}
