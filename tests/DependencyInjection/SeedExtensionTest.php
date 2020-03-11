<?php

namespace Evotodi\SeedBundle\Tests\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class SeedExtensionTest extends AbstractSeedExtensionTest
{
	/**
	 * @param ContainerBuilder $container
	 * @param $resource
	 * @throws Exception
	 */
    protected function loadConfiguration(ContainerBuilder $container, $resource)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../fixtures/Resources/'));
        $loader->load($resource.'.yml');
    }
}
