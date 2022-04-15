<?php

namespace Evotodi\SeedBundle\Model;

use Symfony\Component\Console\Input\InputInterface;

interface SeedExtensionInterface
{
    /**
     * Apply the extension to the commands.
     *
     * $commands the commands list
     * $input    the input
     *
     * return the new commands list
     */
    public function apply(array &$commands, InputInterface $input);
}
