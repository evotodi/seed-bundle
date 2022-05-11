<?php
/**
 * Created by PhpStorm.
 * User: Justin
 * Date: 8/29/2017
 * Time: 11:32 AM
 * Comments: https://github.com/soyuka/SeedBundle
 */

namespace App\DataSeeds;

use App\Entity\States;
use Evotodi\SeedBundle\Command\Seed;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class USStatesSeed extends Seed
{
    public static function seedName(): string
    {
        return 'evo:state:us';
    }

    public static function getOrder(): int
    {
        return 0;
    }

	public function load(InputInterface $input, OutputInterface $output): int
    {

		//Doctrine logging eats a lot of memory, this is a wrapper to disable logging
		$this->disableDoctrineLogging();

		//Access doctrine through $this->doctrine
		$statesRepository = $this->getManager()->getRepository(States::class);


		foreach ($this->getStates() as $abv => $name) {

			if($statesRepository->findOneBy(array('abv' => $abv))) {
				continue;
			}

			$em = new States();

			$em->setAbv($abv);
			$em->setName($name);

			//Doctrine manager is also available
			$this->getManager()->persist($em);

			$this->getManager()->flush();
		}

		$this->getManager()->clear();
		return 0;
	}

	public function unload(InputInterface $input, OutputInterface $output): int
    {
		$className = $this->getManager()->getClassMetadata(States::class)->getName();
		$this->getManager()->createQuery('DELETE FROM '.$className)->execute();
		return 0;
	}

	public function getStates(): array
    {
        return array (
            'AL'=>'Alabama',
            'AK'=>'Alaska',
            'AZ'=>'Arizona',
            'AR'=>'Arkansas',
            'CA'=>'California',
            'CO'=>'Colorado',
            'CT'=>'Connecticut',
            'DE'=>'Delaware',
            'DC'=>'District of Columbia',
            'FL'=>'Florida',
            'GA'=>'Georgia',
            'HI'=>'Hawaii',
            'ID'=>'Idaho',
            'IL'=>'Illinois',
            'IN'=>'Indiana',
            'IA'=>'Iowa',
            'KS'=>'Kansas',
            'KY'=>'Kentucky',
            'LA'=>'Louisiana',
            'ME'=>'Maine',
            'MD'=>'Maryland',
            'MA'=>'Massachusetts',
            'MI'=>'Michigan',
            'MN'=>'Minnesota',
            'MS'=>'Mississippi',
            'MO'=>'Missouri',
            'MT'=>'Montana',
            'NE'=>'Nebraska',
            'NV'=>'Nevada',
            'NH'=>'New Hampshire',
            'NJ'=>'New Jersey',
            'NM'=>'New Mexico',
            'NY'=>'New York',
            'NC'=>'North Carolina',
            'ND'=>'North Dakota',
            'OH'=>'Ohio',
            'OK'=>'Oklahoma',
            'OR'=>'Oregon',
            'PA'=>'Pennsylvania',
            'RI'=>'Rhode Island',
            'SC'=>'South Carolina',
            'SD'=>'South Dakota',
            'TN'=>'Tennessee',
            'TX'=>'Texas',
            'UT'=>'Utah',
            'VT'=>'Vermont',
            'VA'=>'Virginia',
            'WA'=>'Washington',
            'WV'=>'West Virginia',
            'WI'=>'Wisconsin',
            'WY'=>'Wyoming',
        );
	}
}
