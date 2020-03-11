<?php

namespace Evotodi\SeedBundle\Tests\Command;

use Evotodi\SeedBundle\Tests\App\AppKernel;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;

use Symfony\Component\Console\Tester\CommandTester;
use Evotodi\SeedBundle\Tests\fixtures\BadSeed;
use Evotodi\SeedBundle\Tests\fixtures\FailSeed;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SeedsCommandTest extends TestCase
{
	/**
	 * @var ContainerInterface
	 */
	private $container;
	/**
	 * @var Application
	 */
	private $application;
	/**
	 * @var AppKernel
	 */
	private $kernel;

	protected function setUp(): void
    {
	    $this->kernel = new AppKernel();
	    $this->kernel->boot();
	    $this->container = $this->kernel->getContainer();
	    $this->application = new Application($this->kernel);
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

    public function testNoSeeds()
    {
        $application = new Application($this->kernel);
        $application->add($this->container->get('seed.load_seeds_command'));

        $command = $application->find('seed:load');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(), '--skip' => ['foo:bar', 'country', 'town', 'street', 'postcode']]);

        $this->assertRegExp('/No seeds/', $commandTester->getDisplay());
        $this->assertEquals($commandTester->getStatusCode(), 1);
    }

    public function testLoadSeeds()
    {
        $this->seedsLoader();

        $command = $this->application->find('seed:load');

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName()]);

        $output = $commandTester->getDisplay();

        $this->assertRegExp('/Load country/', $output);
        $this->assertRegExp('/Load town/', $output);
        $this->assertRegExp('/Load street/', $output);
        $this->assertRegExp('/Load postcode/', $output);
        $this->assertEquals($commandTester->getStatusCode(), 0);

    }

    public function testUnloadSeeds()
    {
        $this->seedsLoader();

        $command = $this->application->find('seed:unload');

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName()]);

        $output = $commandTester->getDisplay();

        $this->assertRegExp('/Unload country/', $output);
        $this->assertRegExp('/Unload town/', $output);
        $this->assertRegExp('/Unload street/', $output);
        $this->assertRegExp('/Unload postcode/', $output);
        $this->assertEquals($commandTester->getStatusCode(), 0);
    }

    public function testNamedSeeds()
    {
        $this->seedsLoader();

        $command = $this->application->find('seed:load');

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(), 'seeds' => ['Country']]);

        $output = $commandTester->getDisplay();

        $this->assertRegExp('/Load country/', $output);
        $this->assertNotRegExp('/Load town/', $output);
        $this->assertNotRegExp('/Load street/', $output);
        $this->assertNotRegExp('/Load postcode/', $output);
        $this->assertEquals($commandTester->getStatusCode(), 0);
    }

    public function testGlobSeeds()
    {
        $this->seedsLoader();

        $command = $this->application->find('seed:load');

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(), 'seeds' => ['foo:*']]);

        $output = $commandTester->getDisplay();

        $this->assertRegExp('/Load foo:bar/', $output);
        $this->assertEquals($commandTester->getStatusCode(), 0);
    }

    public function testSkipSeeds()
    {
        $this->seedsLoader();

        $command = $this->application->find('seed:load');

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(), '--skip' => 'Town']);

        $output = $commandTester->getDisplay();

        $this->assertRegExp('/Load country/', $output);
        $this->assertNotRegExp('/Load town/', $output);
        $this->assertRegExp('/Load street/', $output);
        $this->assertRegExp('/Load postcode/', $output);
        $this->assertEquals($commandTester->getStatusCode(), 0);
    }

    public function testBadSeed()
    {
    	$this->expectException(InvalidArgumentException::class);
        $application = new Application($this->kernel);
        $application->add(new BadSeed($this->container->getParameter('seed.prefix')));
    }

    public function testBreakSeed()
    {
        $this->seedsLoader();

        $this->application->add(new FailSeed($this->container));

        $command = $this->application->find('seed:load');

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(), '-b' => true]);

        $output = $commandTester->getDisplay();

        $this->assertNotRegExp('/Load country/', $output);
        $this->assertNotRegExp('/Load town/', $output);
        $this->assertNotRegExp('/Load street/', $output);
        $this->assertNotRegExp('/Load postcode/', $output);
        $this->assertRegExp('/seed:fail failed/', $output);
        $this->assertEquals($commandTester->getStatusCode(), 1);
    }

    public function testDebugSeed()
    {
        $this->seedsLoader();
        $command = $this->application->find('seed:load');

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(), '-d' => true]);

        $output = $commandTester->getDisplay();

        $this->assertNotRegExp('/Load country/', $output);
        $this->assertNotRegExp('/Load town/', $output);
        $this->assertNotRegExp('/Load street/', $output);
        $this->assertNotRegExp('/Load postcode/', $output);
        $this->assertRegExp('/Starting seed:country/', $output);
        $this->assertRegExp('/Starting seed:town/', $output);
        $this->assertRegExp('/Starting seed:street/', $output);
        $this->assertRegExp('/Starting seed:postcode/', $output);
        $this->assertEquals($commandTester->getStatusCode(), 0);
    }

    public function testFromSeed()
    {
      $this->seedsLoader();

      $command = $this->application->find('seed:load');

      $commandTester = new CommandTester($command);
      $commandTester->execute(['command' => $command->getName(), '-f' => 'seed:street']);

      $output = $commandTester->getDisplay();

      $this->assertNotRegExp('/Load country/', $output);
      $this->assertNotRegExp('/Load town/', $output);
      $this->assertRegExp('/Load street/', $output);
      $this->assertRegExp('/Load postcode/', $output);
      $this->assertEquals($commandTester->getStatusCode(), 0);

    }

}
