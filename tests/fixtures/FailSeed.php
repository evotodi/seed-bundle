<?php

namespace Evotodi\SeedBundle\Tests\fixtures;

use Evotodi\SeedBundle\Command\Seed;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FailSeed extends Seed
{
    protected function configure()
    {
        $this
            ->setSeedName('fail');

        parent::configure();
    }

    public function load(InputInterface $input, OutputInterface $output): int
    {
        return 1;
    }

    public function unload(InputInterface $input, OutputInterface $output): int
    {
        return 1;
    }

    public function getOrder(): int
    {
        return 0;
    }
}
