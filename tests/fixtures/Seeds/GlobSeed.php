<?php

namespace Evotodi\SeedBundle\Tests\fixtures\Seeds;

use Evotodi\SeedBundle\Command\Seed;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Evotodi\SeedBundle\Model\SeedInterface;

class GlobSeed extends Seed implements SeedInterface
{
    protected function configure()
    {
        $this
            ->setSeedName('foo:bar');

        parent::configure();
    }

    public function load(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Load foo:bar');
	    return 0;
    }

    public function unload(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Unload foo:bar');
	    return 0;
    }
}
