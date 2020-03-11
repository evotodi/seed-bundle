<?php

namespace Evotodi\SeedBundle\Tests\App;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Evotodi\SeedBundle\EvotodiSeedBundle;
use Exception;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Routing\RouteCollectionBuilder;

class AppKernel extends Kernel
{
	use MicroKernelTrait;

	public function __construct()
	{
		parent::__construct('test', true);
	}

    public function registerBundles()
    {
        $bundles = array(
            new FrameworkBundle(),
            new DoctrineBundle(),
            new EvotodiSeedBundle(),
        );

        return $bundles;
    }

	public function getCacheDir()
	{
		return __DIR__.'/cache/'.spl_object_hash($this);
	}

	/**
	 * @inheritDoc
	 */
	protected function configureRoutes(RouteCollectionBuilder $routes)
	{
	}

	/**
	 * @inheritDoc
	 * @throws Exception
	 */
	protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader)
	{
		$loader->load(__DIR__.'/config.yml');
	}
}
