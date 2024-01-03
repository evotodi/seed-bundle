<?php

namespace Evotodi\SeedBundle\DependencyInjection;

use Evotodi\SeedBundle\Interfaces\SeedInterface;
use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class EvotodiSeedExtension extends Extension
{
	/**
	 * @throws Exception
	 */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $container->registerForAutoconfiguration(SeedInterface::class)->addTag('seed.seed');

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

	public function getAlias(): string
    {
		return 'evo_seed';
	}
}
