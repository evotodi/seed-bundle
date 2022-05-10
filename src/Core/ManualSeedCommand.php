<?php

namespace Evotodi\SeedBundle\Core;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ManualSeedCommand extends SeedCoreCommand
{
    protected function configure(): void
    {
        $this->method = null;

        $this
            ->addArgument('method', InputArgument::REQUIRED, 'load/unload seed')
        ;
        $this->baseConfigure();
    }
}
