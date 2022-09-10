<?php

declare(strict_types=1);

namespace Sword\SwordBundle\Test\Store;

use Sword\SwordBundle\Store\WordpressWidgetStore;
use PHPUnit\Framework\TestCase;

class WordpressWidgetStoreTest extends TestCase
{
    public function testAddingWidgets(): void
    {
        $widgets = ['Some\\Widget', 'Some\\OtherWidget'];

        $store = new WordpressWidgetStore();
        $store->addWidget($widgets[0]);
        $store->addWidget($widgets[1]);

        $this->assertSame(array_combine($widgets, $widgets), $store->getWidgets());
    }
}
