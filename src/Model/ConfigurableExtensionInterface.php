<?php

namespace Evotodi\SeedBundle\Model;

use Symfony\Component\Console\Command\Command;

interface ConfigurableExtensionInterface
{
    /**
     * Configure the command from the extension.
     */
    public function configure(Command $command);

    /**
     * Get extension help.
     */
    public function getHelp(): string;
}
