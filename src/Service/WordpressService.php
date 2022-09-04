<?php

declare(strict_types=1);

namespace Sword\SwordBundle\Service;

interface WordpressService
{
    /**
     * Priority given to the service for its initialization. Higher number means higher priority. Defaults to 0.
     */
    public function getPriority(): int;

    public function initialize(): void;
}
