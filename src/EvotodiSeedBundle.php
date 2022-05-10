<?php

namespace Evotodi\SeedBundle;

use Evotodi\SeedBundle\DependencyInjection\Compiler\SeedPass;
use Evotodi\SeedBundle\DependencyInjection\EvotodiSeedExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EvotodiSeedBundle extends Bundle
{
	public function getContainerExtension(): ?ExtensionInterface
	{
		if(null === $this->extension){
			$this->extension = new EvotodiSeedExtension();
		}

		return $this->extension;
	}

	public function build(ContainerBuilder $container)
	{
		$this->container = $container;

        parent::build($container);
        $container->addCompilerPass(new SeedPass());
	}
}
