<?php

namespace Evotodi\SeedBundle\Model;

use Symfony\Component\Console\Command\Command;

interface ConfigurableExtensionInterface
{
    /**
     * Configure the command from the extension.
     *
     * @param Command $command the command to configure
     */
    public function configure(Command $command);

    /**
     * Get extension help.
     *
     * @return string
     */
    public function getHelp();
}
