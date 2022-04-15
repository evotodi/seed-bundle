<?php

namespace Evotodi\SeedBundle\Tests\DependencyInjection;

//http://egeloen.fr/2013/12/08/unit-test-your-symfony2-bundle-di-like-a-boss/

use Evotodi\SeedBundle\DependencyInjection\EvotodiSeedExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;

abstract class AbstractSeedExtensionTest extends TestCase
{
    private EvotodiSeedExtension $extension;
    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $this->extension = new EvotodiSeedExtension();
        $this->container = new ContainerBuilder();
        $this->container->registerExtension($this->extension);
    }

    abstract protected function loadConfiguration(ContainerBuilder $container, $resource);

	public function testWithoutConfiguration()
	{
		$this->expectException(InvalidConfigurationException::class);

		$this->container->loadFromExtension($this->extension->getAlias());
		$this->container->compile();
	}

	public function testCompleteConfiguration()
	{
		$this->loadConfiguration($this->container, 'complete');
		$this->container->loadFromExtension($this->extension->getAlias());
		$this->container->compile();

		$this->assertTrue($this->container->hasParameter('seed.namespace'));
		$this->assertTrue($this->container->hasParameter('seed.directory'));
		$this->assertEquals('Evotodi\SeedBundle\Tests\fixtures\Seeds', $this->container->getParameter('seed.namespace'));
		$this->assertEquals('tests/fixtures/Seeds', $this->container->getParameter('seed.directory'));
	}

	public function testDirectoryConfiguration()
	{
		$this->expectException(InvalidConfigurationException::class);
		$this->expectExceptionMessage("EvotodiSeedBundle config file is missing the namespace");
		$this->loadConfiguration($this->container, 'directory');
		$this->container->loadFromExtension($this->extension->getAlias());
		$this->container->compile();

	}

	public function testNamespaceConfiguration()
	{
		$this->expectException(InvalidConfigurationException::class);
		$this->expectExceptionMessage("EvotodiSeedBundle config file is missing the directory");
		$this->loadConfiguration($this->container, 'namespace');
		$this->container->loadFromExtension($this->extension->getAlias());
		$this->container->compile();

	}

}
