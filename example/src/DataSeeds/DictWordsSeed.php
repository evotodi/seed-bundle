<?php

namespace App\DataSeeds;

use App\Entity\DictWords;
use Evotodi\SeedBundle\Command\Seed;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DictWordsSeed  extends Seed
{
    public static function seedName(): string
    {
        return 'dictWords';
    }

    public static function getOrder(): int
    {
        return 1;
    }

    /**
	 * Load a seed.
	 */
	public function load(InputInterface $input, OutputInterface $output): int
    {
		//Doctrine logging eats a lot of memory, this is a wrapper to disable logging
		$this->disableDoctrineLogging();

		//Access doctrine through $this->doctrine
		$dictWordsRepository = $this->getManager()->getRepository(DictWords::class);


		foreach ($this->getWords() as $word) {

			if($dictWordsRepository->findOneBy(array('word' => $word))) {
				continue;
			}

			$em = new DictWords();

			$em->setWord($word);

			//Doctrine manager is also available
			$this->getManager()->persist($em);

			$this->getManager()->flush();
		}

		$this->getManager()->clear();
		return 0;
	}

	public function unload(InputInterface $input, OutputInterface $output): int
    {
		$className = $this->getManager()->getClassMetadata(DictWords::class )->getName();
		$this->getManager()->createQuery('DELETE FROM '.$className)->execute();
		return 0;
	}

	public function getWords(): bool|array
    {
		$words = file(__DIR__.'/../Resources/dictWords.txt');
		shuffle($words);
		return array_slice($words, 0, 1000);
	}
}
