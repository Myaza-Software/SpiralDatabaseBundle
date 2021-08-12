<?php
/**
 * Spiral Database Bundle
 *
 * @author Vlad Shashkov <root@myaza.info>
 * @copyright Copyright (c) 2021, The Myaza Software
 */

declare(strict_types=1);

namespace Spiral\Bundle\Database\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('spiral_dbal');

        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $this->addDbalSection($rootNode);

        return $treeBuilder;
    }

    private function addDbalSection(ArrayNodeDefinition $node): void
    {
        /*
         * @phpstan-ignore-next-line
         */
        $node
            ->children()
                ->scalarNode('default')
                    ->isRequired()
                    ->defaultValue('default')
                ->end()
            ->end()
            ->children()
                ->arrayNode('aliases')
                    ->scalarPrototype()
                    ->end()
                ->end()
            ->end()
            ->children()
                ->arrayNode('databases')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('connection')
                            ->end()
                            ->scalarNode('tablePrefix')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->children()
                ->arrayNode('connections')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('driver')
                            ->end()
                            ->arrayNode('options')
                                ->scalarPrototype()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
