<?php

namespace Evotodi\SeedBundle\Tests\fixtures;

use Evotodi\SeedBundle\Command\Seed;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GlobSeedB extends Seed
{
    public static function seedName(): string
    {
        return 'foo:baz';
    }

    public function load(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Load foo:baz');
	    return 0;
    }

    public function unload(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Unload foo:baz');
	    return 0;
    }
}
