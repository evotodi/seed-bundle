<?php

namespace Evotodi\SeedBundle\Model;

use Symfony\Component\Console\Input\InputInterface;

interface SeedExtensionInterface
{
    /**
     * Apply the extension to the commands.
     *
     * @param array          $commands the commands list
     * @param InputInterface $input    the input
     *
     * @return array $commands - the new commands list
     */
    public function apply(array &$commands, InputInterface $input);
}
