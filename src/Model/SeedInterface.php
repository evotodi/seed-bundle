<?php

namespace Evotodi\SeedBundle\Model;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Interface SeedInterface.
 */
interface SeedInterface
{
    /**
     * Load a seed.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function load(InputInterface $input, OutputInterface $output);

    /**
     * Unload a seed.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function unload(InputInterface $input, OutputInterface $output);

	/**
	 * get the seed order (default is 0).
	 *
	 * @return int
	 */
	public function getOrder(): int;
}
