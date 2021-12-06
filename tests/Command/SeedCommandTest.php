<?php

namespace Evotodi\SeedBundle\Tests\Command;

use Evotodi\SeedBundle\Tests\App\AppKernel;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SeedCommandTest extends TestCase
{
	private ContainerInterface $container;
	private Application $application;

    protected function setUp():void
    {
	    $kernel = new AppKernel();
	    $kernel->boot();
	    $this->container = $kernel->getContainer();
	    $this->application = new Application($kernel);
        $this->seedsLoader();
    }

    protected function seedsLoader()
    {
        $seeds = $this->container->get('seed.loader');
        $seeds->loadSeeds($this->application);

        $this->assertTrue($this->application->has('seed:country'));
        $this->assertTrue($this->application->has('seed:town'));
        $this->assertTrue($this->application->has('seed:street'));
        $this->assertTrue($this->application->has('seed:postcode'));
    }

    public function testIsValidSeed()
    {
        $c = $this->application->get('seed:country');
        $this->assertTrue(method_exists($c, 'disableDoctrineLogging'));
        $this->assertObjectHasAttribute('manager', $c);
    }

    public function testSeedCommand()
    {
    	$this->expectException(InvalidArgumentException::class);
    	$this->expectExceptionMessage("Method should be one of: load, unload");
        $command = $this->application->find('seed:country');

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(), 'method' => 'nonexistant']);

        $this->assertEquals(1, $commandTester->getStatusCode());
    }
}
