<?php
/**
 * Created by PhpStorm.
 * User: Justin
 * Date: 8/31/2017
 * Time: 3:48 PM
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class States
 * @ORM\Entity(repositoryClass="App\Repository\DictWordsRepository")
 * @ORM\Table(name="misc_dictwords")
 */
class DictWords
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
	private string $word;

	public function getId(): int
    {
		return $this->id;
	}

	public function setId(int $id): self
    {
		$this->id = $id;
        return $this;
	}

	public function getWord(): string
    {
		return $this->word;
	}

	public function setWord(string $word): self
	{
		$this->word = $word;
        return $this;
	}
}
