<?php

namespace Olveneer\TwigComponentsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package Olveneer\TwigComponentsBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{

    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('twig_components');

        $rootNode
            ->children()
                ->arrayNode('templating')
                    ->prototype('scalar')->end()
                    ->defaultValue(['render_function' => 'component', 'access_function' => 'access'])
                    ->children()
                        ->variableNode('access_function')->end()
                        ->variableNode('render_function')->end()
                    ->end()
                ->end() // templating
            ->variableNode('components_directory')->defaultValue('/components')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
