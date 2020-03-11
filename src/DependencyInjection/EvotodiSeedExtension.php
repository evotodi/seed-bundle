<?php

namespace Evotodi\SeedBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class EvotodiSeedExtension extends Extension
{
	/**
	 * @param array $configs
	 * @param ContainerBuilder $container
	 * @throws Exception
	 */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if(!isset($config['namespace'])){
	        throw new InvalidConfigurationException("EvotodiSeedBundle config file is missing the namespace");
        }

	    if(!isset($config['directory'])){
		    throw new InvalidConfigurationException("EvotodiSeedBundle config file is missing the directory");
	    }

        $container->setParameter('seed.namespace', $config['namespace']);
	    $container->setParameter('seed.directory', $config['directory']);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

	public function getAlias()
	{
		return 'evo_seed';
	}
}
