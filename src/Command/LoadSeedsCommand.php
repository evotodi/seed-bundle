<?php

namespace Evotodi\SeedBundle\Command;

use Evotodi\SeedBundle\Core\SeedCoreCommand;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'seed:load',
    description: 'Load a seed',
)]
final class LoadSeedsCommand extends SeedCoreCommand
{
	protected function configure(): void
    {
        $this->method = 'load';
        parent::configure();
    }
}
