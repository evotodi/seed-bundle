<?php

namespace Evotodi\SeedBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('evo_seed');
	    $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('directory')->info('The seeds directory')->end()
	            ->scalarNode('namespace')->info('The seeds namespace')->end()
            ->end();

        return $treeBuilder;
    }
}
