<?php

namespace Evotodi\SeedBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ExtensionPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $extensions = [];

        foreach ($container->findTaggedServiceIds('seed.extension') as $serviceId => $tags) {
            foreach ($tags as $tag) {
                $priority = isset($tag['priority']) ? $tag['priority'] : 0;
                $extensions[$priority][] = new Reference($serviceId);
            }
        }

        ksort($extensions);

        $container->getDefinition('seed.seeds')
            ->addArgument(call_user_func_array('array_merge', $extensions));
    }
}
