<?php

namespace Evotodi\SeedBundle\Tests\fixtures;

use Evotodi\SeedBundle\Command\Seed;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TownSeed extends Seed
{
    public static function seedName(): string
    {
        return 'town';
    }

    public static function getOrder(): int
    {
        return 2;
    }

    public function load(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Load town');
	    return 0;
    }

    public function unload(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Unload town');
	    return 0;
    }
}
