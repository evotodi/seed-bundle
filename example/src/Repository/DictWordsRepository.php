<?php

namespace App\Repository;

use App\Entity\DictWords;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DictWords|null find($id, $lockMode = null, $lockVersion = null)
 * @method DictWords|null findOneBy(array $criteria, array $orderBy = null)
 * @method DictWords[]    findAll()
 * @method DictWords[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DictWordsRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, DictWords::class);
	}

	public function findRandWords($count)
	{
		$em = $this->getEntityManager();
		return $em->createQuery(
			"SELECT word FROM AppBundle:DictWords AS d ORDER BY RAND() LIMIT 0,3; "
		)->getResult();
	}

	public function findRandWordsNew($count): array
    {
		$em = $this->getEntityManager();
		$words = $em->getRepository(DictWords::class)->findAll();
		shuffle($words);
		return array_slice($words, 0 , $count);
	}
}
