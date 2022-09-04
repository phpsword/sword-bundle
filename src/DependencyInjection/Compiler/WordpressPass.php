<?php

declare(strict_types=1);

namespace Sword\SwordBundle\DependencyInjection\Compiler;

use Sword\SwordBundle\Store\WordpressWidgetStore;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class WordpressPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $this->processWidgets($container);
        $this->processPublicServices($container);
    }

    private function processWidgets(ContainerBuilder $container): void
    {
        $widgetStore = $container->findDefinition(WordpressWidgetStore::class);
        $taggedServices = $container->findTaggedServiceIds('sword.wordpress_widget');

        foreach (array_keys($taggedServices) as $widget) {
            $widgetStore->addMethodCall('addWidget', [$widget]);
        }
    }

    /**
     * @param ContainerBuilder $container
     * @return void
     */
    private function processPublicServices(ContainerBuilder $container): void
    {
        $publicServices = $container->getParameter('sword.public_services');

        foreach ($publicServices as $service) {
            $definition = $container->findDefinition($service);

            if ($container->hasAlias($service)) {
                $alias = $container->getAlias($service);
                $alias->setPublic(true);
            } else {
                $definition->setPublic(true);
            }
        }
    }
}
