<?php

namespace Evotodi\SeedBundle\Tests\fixtures;

use Evotodi\SeedBundle\Command\Seed;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FailSeed extends Seed
{
    public static function seedName(): string
    {
        return 'fail';
    }

    public function load(InputInterface $input, OutputInterface $output): int
    {
        return 1;
    }

    public function unload(InputInterface $input, OutputInterface $output): int
    {
        return 1;
    }
}
