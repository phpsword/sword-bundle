<?php

declare(strict_types=1);

namespace Sword\SwordBundle\Loader;

use Sword\SwordBundle\Store\WordpressWidgetStore;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class LazyServiceInstantiator
{
    public function __construct(
        private readonly WordpressWidgetStore $widgetsStore,
        #[Autowire('%kernel.project_dir%')] private readonly string $projectDirectory,
    ) {
    }

    public function requireAll(): void
    {
        foreach ($this->widgetsStore->getWidgets() as $widget) {
            $this->load($widget);
            add_action('widgets_init', static fn () => register_widget($widget));
        }
    }

    private function load(string $class): void
    {
        $prefix = 'App\\';
        $baseDir = $this->projectDirectory . '/src/';

        $length = \strlen($prefix);

        if (strncmp($prefix, $class, $length) !== 0) {
            return;
        }

        $relativeClass = substr($class, $length);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

        if (file_exists($file)) {
            require $file;
        }
    }
}
