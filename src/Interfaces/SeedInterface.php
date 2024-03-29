<?php

namespace Evotodi\SeedBundle\Interfaces;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface SeedInterface
{
    /**
     * return the seed name
     */
    public static function seedName(): string;

    /**
     * Load a seed.
     */
    public function load(InputInterface $input, OutputInterface $output): int;

    /**
     * Unload a seed.
     */
    public function unload(InputInterface $input, OutputInterface $output): int;

    /**
     * get the seed order (default is 0).
     */
    public static function getOrder(): int;
}
