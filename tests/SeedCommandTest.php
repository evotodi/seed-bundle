<?php

use Evotodi\SeedBundle\Core\SeedRegistry;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Exception\CommandNotFoundException;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

class SeedCommandTest extends KernelTestCase
{
    static ?string $cacheDir = null;
    private Application $application;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->application = new Application($kernel);
        self::$cacheDir = self::$kernel->getCacheDir();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        /** Remove the cache dir */
        if(!is_null(self::$cacheDir) and self::$cacheDir != '/') {
            (new Filesystem())->remove(self::$cacheDir);
        }
    }

    /** @dataProvider seedProvider */
    public function testSeedsExist(string $seed)
    {
        $command = $this->application->find($seed);
        $this->assertEquals($seed, $command->getName());
    }

    public function seedProvider(): Generator
    {
        yield 'Country Seed' => ['seed:country'];
        yield 'Glob Seed A' => ['seed:foo:bar'];
        yield 'Glob Seed B' => ['seed:foo:baz'];
        yield 'Postcode Seed' => ['seed:postcode'];
        yield 'Street Seed' => ['seed:street'];
        yield 'Town Seed' => ['seed:town'];
        yield 'Fail Seed' => ['seed:fail'];
    }

    public function testBadSeedNotExist()
    {
        $this->expectException(CommandNotFoundException::class);
        $this->application->find("seed:bad");
    }

    public function testSeedCommandMethod()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Method should be one of: load, unload");
        $command = $this->application->find('seed:country');

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(), 'method' => 'nonexistant']);

        $this->assertEquals(1, $commandTester->getStatusCode());
    }

    public function testNoSeeds()
    {
        $tokenServiceMock = $this->getMockBuilder(SeedRegistry::class)
            ->disableOriginalConstructor()
            ->getMock();
        self::getContainer()->set('seed.registry', $tokenServiceMock);

        $command = $this->application->find('seed:load');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName()]);

        $this->assertMatchesRegularExpression('/No seeds found/', $commandTester->getDisplay());
        $this->assertEquals(1, $commandTester->getStatusCode());
    }

    public function testLoadSeeds()
    {
        $command = $this->application->find('seed:load');

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName()]);

        $output = $commandTester->getDisplay();

        $this->assertMatchesRegularExpression("/Starting load Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\CountrySeed/", $output);
        $this->assertMatchesRegularExpression("/Seed done Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\CountrySeed/", $output);

        $this->assertMatchesRegularExpression("/Starting load Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\TownSeed/", $output);
        $this->assertMatchesRegularExpression("/Seed done Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\TownSeed/", $output);

        $this->assertMatchesRegularExpression("/Starting load Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\StreetSeed/", $output);
        $this->assertMatchesRegularExpression("/Seed done Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\StreetSeed/", $output);

        $this->assertMatchesRegularExpression("/Starting load Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\PostcodeSeed/", $output);
        $this->assertMatchesRegularExpression("/Seed done Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\PostcodeSeed/", $output);

        $this->assertMatchesRegularExpression("/Starting load Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\GlobSeedA/", $output);
        $this->assertMatchesRegularExpression("/Seed done Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\GlobSeedA/", $output);

        $this->assertMatchesRegularExpression("/Starting load Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\GlobSeedB/", $output);
        $this->assertMatchesRegularExpression("/Seed done Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\GlobSeedB/", $output);

        $this->assertMatchesRegularExpression("/Starting load Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\FailSeed/", $output);
        $this->assertMatchesRegularExpression("/Failed processing seed Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\FailSeed/", $output);

        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testUnloadSeeds()
    {
        $command = $this->application->find('seed:unload');

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName()]);

        $output = $commandTester->getDisplay();

        $this->assertMatchesRegularExpression("/Starting unload Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\CountrySeed/", $output);
        $this->assertMatchesRegularExpression("/Seed done Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\CountrySeed/", $output);

        $this->assertMatchesRegularExpression("/Starting unload Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\TownSeed/", $output);
        $this->assertMatchesRegularExpression("/Seed done Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\TownSeed/", $output);

        $this->assertMatchesRegularExpression("/Starting unload Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\StreetSeed/", $output);
        $this->assertMatchesRegularExpression("/Seed done Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\StreetSeed/", $output);

        $this->assertMatchesRegularExpression("/Starting unload Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\PostcodeSeed/", $output);
        $this->assertMatchesRegularExpression("/Seed done Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\PostcodeSeed/", $output);

        $this->assertMatchesRegularExpression("/Starting unload Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\GlobSeedA/", $output);
        $this->assertMatchesRegularExpression("/Seed done Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\GlobSeedA/", $output);

        $this->assertMatchesRegularExpression("/Starting unload Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\GlobSeedB/", $output);
        $this->assertMatchesRegularExpression("/Seed done Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\GlobSeedB/", $output);

        $this->assertMatchesRegularExpression("/Starting unload Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\FailSeed/", $output);
        $this->assertMatchesRegularExpression("/Failed processing seed Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\FailSeed/", $output);
        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testNamedSeeds()
    {
        $command = $this->application->find('seed:load');

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(), 'seeds' => ['country']]);

        $output = $commandTester->getDisplay();

        $this->assertMatchesRegularExpression("/Starting load Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\CountrySeed/", $output);
        $this->assertMatchesRegularExpression("/Seed done Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\CountrySeed/", $output);

        $this->assertDoesNotMatchRegularExpression("/Starting load Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\TownSeed/", $output);
        $this->assertDoesNotMatchRegularExpression("/Seed done Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\TownSeed/", $output);

        $this->assertDoesNotMatchRegularExpression("/Starting load Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\StreetSeed/", $output);
        $this->assertDoesNotMatchRegularExpression("/Seed done Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\StreetSeed/", $output);

        $this->assertDoesNotMatchRegularExpression("/Starting load Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\PostcodeSeed/", $output);
        $this->assertDoesNotMatchRegularExpression("/Seed done Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\PostcodeSeed/", $output);

        $this->assertDoesNotMatchRegularExpression("/Starting load Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\GlobSeedA/", $output);
        $this->assertDoesNotMatchRegularExpression("/Seed done Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\GlobSeedA/", $output);

        $this->assertDoesNotMatchRegularExpression("/Starting load Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\GlobSeedB/", $output);
        $this->assertDoesNotMatchRegularExpression("/Seed done Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\GlobSeedB/", $output);

        $this->assertDoesNotMatchRegularExpression("/Starting load Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\FailSeed/", $output);
        $this->assertDoesNotMatchRegularExpression("/Failed processing seed Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\FailSeed/", $output);

        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testGlobSeeds()
    {
        $command = $this->application->find('seed:load');

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(), 'seeds' => ['foo:*']]);

        $output = $commandTester->getDisplay();

        $this->assertMatchesRegularExpression("/Starting load Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\GlobSeedA/", $output);
        $this->assertMatchesRegularExpression("/Seed done Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\GlobSeedA/", $output);

        $this->assertMatchesRegularExpression("/Starting load Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\GlobSeedB/", $output);
        $this->assertMatchesRegularExpression("/Seed done Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\GlobSeedB/", $output);

        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testSkipSeeds()
    {
        $command = $this->application->find('seed:load');

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(), '--skip' => ['Town', 'fail']]);

        $output = $commandTester->getDisplay();

        $this->assertMatchesRegularExpression("/Starting load Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\CountrySeed/", $output);
        $this->assertMatchesRegularExpression("/Seed done Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\CountrySeed/", $output);

        $this->assertDoesNotMatchRegularExpression("/Starting load Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\TownSeed/", $output);
        $this->assertDoesNotMatchRegularExpression("/Seed done Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\TownSeed/", $output);

        $this->assertMatchesRegularExpression("/Starting load Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\StreetSeed/", $output);
        $this->assertMatchesRegularExpression("/Seed done Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\StreetSeed/", $output);

        $this->assertMatchesRegularExpression("/Starting load Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\PostcodeSeed/", $output);
        $this->assertMatchesRegularExpression("/Seed done Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\PostcodeSeed/", $output);

        $this->assertMatchesRegularExpression("/Starting load Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\GlobSeedA/", $output);
        $this->assertMatchesRegularExpression("/Seed done Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\GlobSeedA/", $output);

        $this->assertMatchesRegularExpression("/Starting load Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\GlobSeedB/", $output);
        $this->assertMatchesRegularExpression("/Seed done Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\GlobSeedB/", $output);

        $this->assertDoesNotMatchRegularExpression("/Starting load Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\FailSeed/", $output);
        $this->assertDoesNotMatchRegularExpression("/Failed processing seed Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\FailSeed/", $output);

        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testBreakSeed()
    {
        $command = $this->application->find('seed:load');

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(), '-b' => true]);

        $output = $commandTester->getDisplay();

        $this->assertDoesNotMatchRegularExpression("/Starting load Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\CountrySeed/", $output);
        $this->assertDoesNotMatchRegularExpression("/Seed done Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\CountrySeed/", $output);

        $this->assertDoesNotMatchRegularExpression("/Starting load Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\TownSeed/", $output);
        $this->assertDoesNotMatchRegularExpression("/Seed done Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\TownSeed/", $output);

        $this->assertDoesNotMatchRegularExpression("/Starting load Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\StreetSeed/", $output);
        $this->assertDoesNotMatchRegularExpression("/Seed done Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\StreetSeed/", $output);

        $this->assertMatchesRegularExpression('/Failed processing seed Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\FailSeed/', $output);
        $this->assertEquals(1, $commandTester->getStatusCode());
    }

    public function testDebugSeed()
    {
        $command = $this->application->find('seed:load');

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(), '-d' => true,  'seeds' => ['country']]);

        $output = $commandTester->getDisplay();

        $this->assertMatchesRegularExpression('/Debug loading Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\CountrySeed/', $output);
        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testFromSeed()
    {
        $command = $this->application->find('seed:load');

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(), '-f' => 'street']);

        $output = $commandTester->getDisplay();

        $this->assertDoesNotMatchRegularExpression("/Starting load Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\CountrySeed/", $output);
        $this->assertDoesNotMatchRegularExpression("/Seed done Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\CountrySeed/", $output);

        $this->assertDoesNotMatchRegularExpression("/Starting load Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\TownSeed/", $output);
        $this->assertDoesNotMatchRegularExpression("/Seed done Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\TownSeed/", $output);

        $this->assertMatchesRegularExpression("/Starting load Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\StreetSeed/", $output);
        $this->assertMatchesRegularExpression("/Seed done Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\StreetSeed/", $output);

        $this->assertMatchesRegularExpression("/Starting load Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\PostcodeSeed/", $output);
        $this->assertMatchesRegularExpression("/Seed done Evotodi\\\\SeedBundle\\\\Tests\\\\fixtures\\\\PostcodeSeed/", $output);

        $this->assertEquals(0, $commandTester->getStatusCode());
    }

}
