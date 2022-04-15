<?php

namespace Evotodi\SeedBundle\Tests\Command;

use Evotodi\SeedBundle\Tests\App\AppKernel;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Console\Application;

use Symfony\Component\Console\Tester\CommandTester;
use Evotodi\SeedBundle\Tests\fixtures\BadSeed;
use Evotodi\SeedBundle\Tests\fixtures\FailSeed;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SeedsCommandTest extends TestCase
{
	private ContainerInterface $container;
	private Application $application;
	private AppKernel $kernel;
    private ManagerRegistry $doctrine;

	protected function setUp(): void
    {
	    $this->kernel = new AppKernel();
	    $this->kernel->boot();
	    $this->container = $this->kernel->getContainer();
        $this->doctrine = $this->container->get('doctrine');
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

        $this->assertMatchesRegularExpression('/No seeds/', $commandTester->getDisplay());
        $this->assertEquals(1, $commandTester->getStatusCode());
    }

    public function testLoadSeeds()
    {
        $this->seedsLoader();

        $command = $this->application->find('seed:load');

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName()]);

        $output = $commandTester->getDisplay();

        $this->assertMatchesRegularExpression('/Load country/', $output);
        $this->assertMatchesRegularExpression('/Load town/', $output);
        $this->assertMatchesRegularExpression('/Load street/', $output);
        $this->assertMatchesRegularExpression('/Load postcode/', $output);
        $this->assertEquals(0, $commandTester->getStatusCode());

    }

    public function testUnloadSeeds()
    {
        $this->seedsLoader();

        $command = $this->application->find('seed:unload');

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName()]);

        $output = $commandTester->getDisplay();

        $this->assertMatchesRegularExpression('/Unload country/', $output);
        $this->assertMatchesRegularExpression('/Unload town/', $output);
        $this->assertMatchesRegularExpression('/Unload street/', $output);
        $this->assertMatchesRegularExpression('/Unload postcode/', $output);
        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testNamedSeeds()
    {
        $this->seedsLoader();

        $command = $this->application->find('seed:load');

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(), 'seeds' => ['Country']]);

        $output = $commandTester->getDisplay();

        $this->assertMatchesRegularExpression('/Load country/', $output);
        $this->assertDoesNotMatchRegularExpression('/Load town/', $output);
        $this->assertDoesNotMatchRegularExpression('/Load street/', $output);
        $this->assertDoesNotMatchRegularExpression('/Load postcode/', $output);
        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testGlobSeeds()
    {
        $this->seedsLoader();

        $command = $this->application->find('seed:load');

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(), 'seeds' => ['foo:*']]);

        $output = $commandTester->getDisplay();

        $this->assertMatchesRegularExpression('/Load foo:bar/', $output);
        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testSkipSeeds()
    {
        $this->seedsLoader();

        $command = $this->application->find('seed:load');

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(), '--skip' => 'Town']);

        $output = $commandTester->getDisplay();

        $this->assertMatchesRegularExpression('/Load country/', $output);
        $this->assertDoesNotMatchRegularExpression('/Load town/', $output);
        $this->assertMatchesRegularExpression('/Load street/', $output);
        $this->assertMatchesRegularExpression('/Load postcode/', $output);
        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testBadSeed()
    {
    	$this->expectException(InvalidArgumentException::class);
        $application = new Application($this->kernel);
        /** @noinspection PhpParamsInspection */
        $application->add(new BadSeed($this->container->getParameter('seed.prefix')));
    }

    public function testBreakSeed()
    {
        $this->seedsLoader();

        $this->application->add(new FailSeed($this->doctrine));

        $command = $this->application->find('seed:load');

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(), '-b' => true]);

        $output = $commandTester->getDisplay();

        $this->assertDoesNotMatchRegularExpression('/Load country/', $output);
        $this->assertDoesNotMatchRegularExpression('/Load town/', $output);
        $this->assertDoesNotMatchRegularExpression('/Load street/', $output);
        $this->assertDoesNotMatchRegularExpression('/Load postcode/', $output);
        $this->assertMatchesRegularExpression('/seed:fail failed/', $output);
        $this->assertEquals(1, $commandTester->getStatusCode());
    }

    public function testDebugSeed()
    {
        $this->seedsLoader();
        $command = $this->application->find('seed:load');

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(), '-d' => true]);

        $output = $commandTester->getDisplay();

        $this->assertDoesNotMatchRegularExpression('/Load country/', $output);
        $this->assertDoesNotMatchRegularExpression('/Load town/', $output);
        $this->assertDoesNotMatchRegularExpression('/Load street/', $output);
        $this->assertDoesNotMatchRegularExpression('/Load postcode/', $output);
        $this->assertMatchesRegularExpression('/Starting seed:country/', $output);
        $this->assertMatchesRegularExpression('/Starting seed:town/', $output);
        $this->assertMatchesRegularExpression('/Starting seed:street/', $output);
        $this->assertMatchesRegularExpression('/Starting seed:postcode/', $output);
        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testFromSeed()
    {
      $this->seedsLoader();

      $command = $this->application->find('seed:load');

      $commandTester = new CommandTester($command);
      $commandTester->execute(['command' => $command->getName(), '-f' => 'seed:street']);

      $output = $commandTester->getDisplay();

      $this->assertDoesNotMatchRegularExpression('/Load country/', $output);
      $this->assertDoesNotMatchRegularExpression('/Load town/', $output);
      $this->assertMatchesRegularExpression('/Load street/', $output);
      $this->assertMatchesRegularExpression('/Load postcode/', $output);
      $this->assertEquals(0, $commandTester->getStatusCode());

    }

}
