<?php

namespace Evotodi\SeedBundle\Command;

use Evotodi\SeedBundle\Core\SeedCoreCommand;
use Symfony\Component\Console\Input\InputArgument;

class Seed extends SeedCoreCommand
{
    protected function configure(): void
    {
        $this->method = null;
        $this->setName(sprintf('seed:%s', $this->seedName()));
        $this->addArgument('method', InputArgument::REQUIRED, 'load/unload seed');
    }
}
