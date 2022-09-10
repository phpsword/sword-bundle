<?php

declare(strict_types=1);

namespace Sword\SwordBundle\Test\Loader;

use Mockery;
use Sword\SwordBundle\Loader\LazyServiceInstantiator;
use Sword\SwordBundle\Store\WordpressWidgetStore;
use Sword\SwordBundle\Test\WordpressTestCase;

use function Brain\Monkey\Actions\expectAdded;

class LazyServiceInstantiatorTest extends WordpressTestCase
{
    public function testRequireAll(): void
    {
        $store = new WordpressWidgetStore();
        $store->addWidget('Some\\Widget');
        $store->addWidget('Some\\OtherWidget');

        $instantiator = new LazyServiceInstantiator($store, __DIR__);

        expectAdded('widgets_init')->twice()->with(Mockery::type('Closure'));
        $instantiator->requireAll();

        $this->assertTrue(true);
    }
}
