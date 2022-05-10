<?php

namespace Evotodi\SeedBundle\Tests\fixtures;

use Evotodi\SeedBundle\Command\Seed;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StreetSeed extends Seed
{
    public static function seedName(): string
    {
        return 'street';
    }

    public static function getOrder(): int
    {
        return 3;
    }

    public function load(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Load street');
	    return 0;
    }

    public function unload(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Unload street');
	    return 0;
    }
}
