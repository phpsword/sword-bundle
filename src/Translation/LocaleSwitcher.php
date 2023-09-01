<?php

declare(strict_types=1);

namespace Sword\SwordBundle\Translation;

use Sword\SwordBundle\Service\AbstractWordpressService;
use Symfony\Component\Translation\LocaleSwitcher as SymfonyLocaleSwitcher;

final class LocaleSwitcher extends AbstractWordpressService
{
    public function __construct(
        private readonly SymfonyLocaleSwitcher $localeSwitcher
    ) {
    }

    public function getPriority(): int
    {
        return 1000;
    }

    public function initialize(): void
    {
        $this->localeSwitcher->setLocale(get_locale());
    }
}
