<?php

declare(strict_types=1);

namespace Sword\SwordBundle\Store;

final class WordpressWidgetStore
{
    private array $widgets = [];

    public function getWidgets(): array
    {
        return $this->widgets;
    }

    public function addWidget(string $widget): void
    {
        $this->widgets[$widget] = $widget;
    }
}
