<?php

namespace App\DataSeeds;
use App\Entity\Test1;
use Evotodi\SeedBundle\Command\Seed;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestSeed extends Seed
{
    public static function seedName(): string
    {
        return 'test';
    }

	public function load(InputInterface $input, OutputInterface $output): int
    {
		//$this->disableDoctrineLogging();
		$test = new Test1();
		$test->setTest1('asdf');

		$this->getManager()->persist($test);
		$this->getManager()->flush();
		return 0;
	}

	public function unload(InputInterface $input, OutputInterface $output): int
    {
		$this->getManager()->getConnection()->executeQuery('DELETE FROM test1');
		return 0;
	}
}
