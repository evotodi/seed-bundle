<?php

namespace Evotodi\SeedBundle\Tests\fixtures;

use Evotodi\SeedBundle\Command\Seed;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CountrySeed extends Seed
{
    public static function seedName(): string
    {
        return 'country';
    }

    public static function getOrder(): int
    {
        return 1;
    }

    public function load(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Load country');
	    return 0;
    }

    public function unload(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Unload country');
	    return 0;
    }
}
