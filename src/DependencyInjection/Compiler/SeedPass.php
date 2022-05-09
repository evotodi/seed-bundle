<?php

namespace Evotodi\SeedBundle\DependencyInjection\Compiler;

use Evotodi\SeedBundle\Exception\InvalidSeedNameException;
use Evotodi\SeedBundle\Model\SeedItem;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class SeedPass implements CompilerPassInterface
{
    /**
     * @throws InvalidSeedNameException
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('seed.registry')) {
            return;
        }

        $definition = $container->findDefinition('seed.registry');

        $taggedServices = $container->findTaggedServiceIds('seed.seed');

        foreach ($taggedServices as $id => $tags){
            try {
                $rc = new ReflectionClass($id);
                if($rc->hasMethod('seedName')){
                    $seedName = $rc->getMethod('seedName')->invoke(null);
                    if(in_array($seedName, ['load', 'unload'])){
                        throw new InvalidSeedNameException();
                    }
                    $seedOrder = $rc->getMethod('getOrder')->invoke(null);

                    $definition->addMethodCall('addSeed', [$seedName, new Reference($id), $seedOrder]);
                }
            } catch (ReflectionException) {
            }

        }
    }
}
