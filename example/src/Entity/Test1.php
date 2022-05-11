<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Test1
 * @ORM\Entity()
 */
class Test1
{
	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @ORM\Column(type="integer")
	 */
	private int $id;

	/**
	 * @ORM\Column(type="string", length=10)
	 */
	private string $test1;

	public function getId(): int
    {
		return $this->id;
	}

	public function setId( int $id): self
	{
		$this->id = $id;
        return $this;
	}

	public function getTest1(): string
    {
		return $this->test1;
	}

	public function setTest1(string $test1): self
	{
		$this->test1 = $test1;
        return $this;
	}


}
