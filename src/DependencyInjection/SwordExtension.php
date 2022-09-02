<?php

namespace Sword\SwordBundle\DependencyInjection;

use NumberNine\Common\Bundle\MergeConfigurationTrait;
use Sword\SwordBundle\Service\WordpressService;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Symfony\Component\Yaml\Yaml;

final class SwordExtension extends ConfigurableExtension implements PrependExtensionInterface
{
    use MergeConfigurationTrait;

    public function getAlias(): string
    {
        return 'sword';
    }

    public function prepend(ContainerBuilder $container): void
    {
        $prioritizedConfigNames = ['security', 'doctrine'];
        $prioritizedConfigs = [];
        $extensions = $container->getExtensions();

        foreach (Yaml::parseFile(__DIR__ . '/../../config/app.yaml') as $name => $config) {
            if (empty($extensions[$name])) {
                continue;
            }

            if (in_array($name, $prioritizedConfigNames, true)) {
                if (!array_key_exists($name, $prioritizedConfigs)) {
                    $prioritizedConfigs[$name] = [];
                }

                $prioritizedConfigs[$name][] = $config;
            } else {
                $this->mergeConfigIntoOne($container, $name, $config);
            }
        }

        foreach ($prioritizedConfigNames as $name) {
            if (empty($prioritizedConfigs[$name])) {
                continue;
            }

            foreach ($prioritizedConfigs[$name] as $config) {
                $this->mergeConfigIntoOne($container, $name, $config, true);
            }
        }
    }

    protected function loadInternal(array $mergedConfig, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.yaml');

        $container->registerForAutoconfiguration(WordpressService::class)
            ->addTag('sword.wordpress_service')
        ;

        $container->setParameter('sword.wordpress_core_dir', $mergedConfig['wordpress_core_dir']);
        $container->setParameter('sword.wordpress_content_dir', $mergedConfig['wordpress_content_dir']);
        $container->setParameter('sword.child_theme_translation_domain', $mergedConfig['child_theme_translation_domain']);
        $container->setParameter('sword.table_prefix', $mergedConfig['table_prefix']);
        $container->setParameter('sword.public_services', $mergedConfig['public_services']);
        $container->setParameter('sword.widgets_path', $mergedConfig['widgets_path']);

        if ($mergedConfig['widgets_path'] && file_exists($mergedConfig['widgets_path'])) {
            $definition = (new Definition())
                ->setLazy(true)
                ->addTag('sword.wordpress_widget')
            ;
            $loader->registerClasses($definition, $mergedConfig['widgets_namespace'], $mergedConfig['widgets_path']);
        }
    }
}
