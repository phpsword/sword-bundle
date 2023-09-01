<?php

namespace Sword\SwordBundle\DependencyInjection;

use Doctrine\Bundle\DoctrineBundle\Dbal\RegexSchemaAssetFilter;
use ReflectionClass;
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
    public function getAlias(): string
    {
        return 'sword';
    }

    public function prepend(ContainerBuilder $container): void
    {
        $swordConfig = $container->getExtensionConfig('sword');
        $prioritizedConfigNames = array_diff(
            ['security', 'doctrine'],
            $swordConfig[0]['overridden_configurations'] ?? []
        );
        $prioritizedConfigs = [];
        $extensions = $container->getExtensions();

        foreach (Yaml::parseFile(__DIR__ . '/../../config/app.yaml') as $name => $config) {
            if (empty($extensions[$name])) {
                continue;
            }

            if (\in_array($name, $prioritizedConfigNames, true)) {
                if (!\array_key_exists($name, $prioritizedConfigs)) {
                    $prioritizedConfigs[$name] = [];
                }

                $prioritizedConfigs[$name][] = $config;
            } else {
                if ($name === 'doctrine') {
                    $config = $this->mergeDoctrineConfig($container, $config);
                }

                $this->mergeConfigIntoOne($container, $name, $config);
            }
        }

        foreach ($prioritizedConfigNames as $name) {
            if (empty($prioritizedConfigs[$name])) {
                continue;
            }

            foreach ($prioritizedConfigs[$name] as $config) {
                if ($name === 'doctrine') {
                    $config = $this->mergeDoctrineConfig($container, $config);
                }

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
        $container->setParameter(
            'sword.child_theme_translation_domain',
            $mergedConfig['child_theme_translation_domain'],
        );
        $container->setParameter('sword.table_prefix', $mergedConfig['table_prefix']);
        $container->setParameter('sword.public_services', $mergedConfig['public_services']);
        $container->setParameter('sword.widgets_path', $mergedConfig['widgets_path']);
        $container->setParameter('sword.app_namespace', $mergedConfig['app_namespace']);

        if ($mergedConfig['widgets_path'] && file_exists($mergedConfig['widgets_path'])) {
            $definition = (new Definition())
                ->setLazy(true)
                ->addTag('sword.wordpress_widget')
            ;
            $loader->registerClasses($definition, $mergedConfig['widgets_namespace'], $mergedConfig['widgets_path']);
        }

        $definition = new Definition(RegexSchemaAssetFilter::class, ['~^(?!(%sword.table_prefix%))~']);
        $definition->addTag('doctrine.dbal.schema_filter', [
            'connection' => 'default',
        ]);
        $container->setDefinition('doctrine.dbal.default_regex_schema_filter', $definition);
    }

    private function mergeDoctrineConfig(ContainerBuilder $container, array $config): array
    {
        $doctrineConfig = $container->getExtensionConfig('doctrine');

        if (isset($doctrineConfig[0]['dbal']['connections'])) {
            if (empty($doctrineConfig[0]['dbal']['connections']['default'])) {
                $doctrineConfig[0]['dbal']['connections']['default'] = $config['dbal'];
            }

            $config['dbal'] = $doctrineConfig[0]['dbal'];
        }

        return $config;
    }

    private function mergeConfigIntoOne(
        ContainerBuilder $container,
        string $name,
        array $config = [],
        bool $reverse = false,
    ): void {
        $originalConfig = $container->getExtensionConfig($name);
        if (!\count($originalConfig)) {
            $originalConfig[] = [];
        }

        $originalConfig[0] = $reverse
            ? $this->mergeDistinct($originalConfig[0], $config)
            : $this->mergeDistinct($config, $originalConfig[0]);

        $this->setExtensionConfig($container, $name, $originalConfig);
    }

    private function setExtensionConfig(ContainerBuilder $container, string $name, array $config = []): void
    {
        $classRef = new ReflectionClass(ContainerBuilder::class);
        $extensionConfigsRef = $classRef->getProperty('extensionConfigs');

        $newConfig = $extensionConfigsRef->getValue($container);
        $newConfig[$name] = $config;
        $extensionConfigsRef->setValue($container, $newConfig);
    }

    private function mergeDistinct(array $first, array $second): array
    {
        foreach ($second as $index => $value) {
            if (\is_int($index) && !\in_array($value, $first, true)) {
                $first[] = $value;
            } elseif (!\array_key_exists($index, $first)) {
                $first[$index] = $value;
            } elseif (\is_array($value)) {
                if (\is_array($first[$index])) {
                    $first[$index] = $this->mergeDistinct($first[$index], $value);
                } else {
                    $first[$index] = $value;
                }
            } else {
                $first[$index] = $value;
            }
        }

        return $first;
    }
}
