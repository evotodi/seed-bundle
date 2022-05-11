<?php
/**
 * Created by PhpStorm.
 * User: Justin
 * Date: 8/29/2017
 * Time: 10:27 AM
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class States
 * @ORM\Entity
 * @ORM\Table(name="misc_states")
 */
class States
{
	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="NONE")
	 * @ORM\Column(type="string", length=10)
	 */
	private string $abv;

	/**
	 * @ORM\Column(type="string", length=50)
	 */
	private string $name;

	public function getAbv(): string
    {
		return $this->abv;
	}

	public function setAbv(string $abv): self
	{
		$this->abv = $abv;
        return $this;
	}

	public function getName(): string
    {
		return $this->name;
	}

	public function setName(string $name): self
	{
		$this->name = $name;
        return $this;
	}

	public function __toString()
	{
		return $this->getAbv();
	}

}
