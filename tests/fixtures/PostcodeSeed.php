<?php

namespace Evotodi\SeedBundle\Tests\fixtures;

use Evotodi\SeedBundle\Command\Seed;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PostcodeSeed extends Seed
{
    public static function seedName(): string
    {
        return 'postcode';
    }

    public static function getOrder(): int
    {
        return 4;
    }

    public function load(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Load postcode');
	    return 0;
    }

    public function unload(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Unload postcode');
	    return 0;
    }
}
