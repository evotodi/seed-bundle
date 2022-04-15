<?php

namespace Evotodi\SeedBundle\Tests\fixtures\Seeds;

use Evotodi\SeedBundle\Command\Seed;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Evotodi\SeedBundle\Model\SeedInterface;
class CountrySeed extends Seed implements SeedInterface
{
    protected function configure()
    {
        $this
            ->setSeedName('country');

        parent::configure();
    }

    /**
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     */
    public function load(InputInterface $input, OutputInterface $output): int
    {
        $this->disableDoctrineLogging();
        $output->writeln('Load country');
	    return 0;
    }

    /**
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     */
    public function unload(InputInterface $input, OutputInterface $output): int
    {
        $this->disableDoctrineLogging();
        $output->writeln('Unload country');
	    return 0;
    }

    public function getOrder(): int
    {
        return 1;
    }
}
