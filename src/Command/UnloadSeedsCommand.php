<?php

namespace Evotodi\SeedBundle\Command;

use Evotodi\SeedBundle\Core\SeedCoreCommand;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'seed:unload',
    description: 'Unload a seed',
)]
final class UnloadSeedsCommand extends SeedCoreCommand
{
	protected function configure(): void
    {
        $this->method = 'unload';
        parent::configure();
    }
}
