<?php

namespace Evotodi\SeedBundle\Tests\fixtures\Seeds;

use Evotodi\SeedBundle\Command\Seed;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Evotodi\SeedBundle\Model\SeedInterface;

class TownSeed extends Seed implements SeedInterface
{
    protected function configure()
    {
        $this
            ->setSeedName('town');

        parent::configure();
    }

    /**
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     */
    public function load(InputInterface $input, OutputInterface $output): int
    {
        $this->disableDoctrineLogging();
        $output->writeln('Load town');
	    return 0;
    }

    /**
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     */
    public function unload(InputInterface $input, OutputInterface $output): int
    {
        $this->disableDoctrineLogging();
        $output->writeln('Unload town');
	    return 0;
    }

    public function getOrder(): int
    {
        return 2;
    }
}
