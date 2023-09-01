<?php

namespace Sword\SwordBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('sword');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('wordpress_core_dir')->defaultValue('%kernel.project_dir%/wp/core')->end()
                ->scalarNode('wordpress_content_dir')->defaultValue('%kernel.project_dir%/wp/content')->end()
                ->scalarNode('child_theme_translation_domain')->isRequired()->end()
                ->scalarNode('child_theme_language_path')->defaultValue('%kernel.project_dir%/translations/%sword.child_theme_translation_domain%')->end()
                ->scalarNode('table_prefix')->defaultValue('wp_')->end()
                ->scalarNode('app_namespace')->defaultValue('App\\')->end()
                ->scalarNode('widgets_namespace')->defaultValue('App\\Widget\\')->end()
                ->scalarNode('widgets_path')->defaultValue('%kernel.project_dir%/src/Widget/')->end()
                ->arrayNode('overridden_configurations')
                    ->scalarPrototype()->end()
                ->end()
                ->arrayNode('public_services')
                    ->scalarPrototype()->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
