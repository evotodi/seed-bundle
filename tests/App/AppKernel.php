<?php

namespace Evotodi\SeedBundle\Tests\App;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Evotodi\SeedBundle\EvotodiSeedBundle;
use Exception;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
	use MicroKernelTrait;

	public function __construct()
	{
		parent::__construct('test', true);
	}

    public function registerBundles(): array
    {
        return array(
            new FrameworkBundle(),
            new DoctrineBundle(),
            new EvotodiSeedBundle(),
        );
    }

	/**
	 * @inheritDoc
	 * @throws Exception
	 */
	protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
		$loader->load(__DIR__.'/config.yaml');
	}
}
