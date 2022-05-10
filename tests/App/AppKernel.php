<?php

namespace Evotodi\SeedBundle\Tests\App;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Evotodi\SeedBundle\DependencyInjection\Compiler\SeedPass;
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
        $bundles = array(
            new FrameworkBundle(),
            new DoctrineBundle(),
            new EvotodiSeedBundle(),
        );

        return $bundles;
    }

	//public function getCacheDir(): string
    //{
	//	return __DIR__.'../../var/cache/test';
	//}

	/**
	 * @inheritDoc
	 */
	//protected function configureRoutes(RouteCollectionBuilder $routes)
	//{
	//}

	/**
	 * @inheritDoc
	 * @throws Exception
	 */
	protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader)
	{
		$loader->load(__DIR__.'/config.yml');
	}

    //protected function build(ContainerBuilder $container)
    //{
    //    parent::build($container);
    //    //$container->addCompilerPass(new SeedPass());
    //}
}
