<?php

namespace Evotodi\SeedBundle\Core;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SeedCore extends Command
{
    /** @var string **/
    private $prefix = 'seed';
    /** @var string **/
    private $seedName;
    /** @var ManagerRegistry **/
    private $doctrine;
    /** @var EntityManagerInterface **/
    protected $manager;
    /** @var ContainerInterface **/
    protected $container;
	/** @var string  */
    protected $separator = ':';

	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
		$this->doctrine = $container->get('doctrine');
		$this->manager = $this->doctrine->getManager();
		parent::__construct();
	}

	public function getManager(): EntityManagerInterface
	{
		return $this->manager;
	}

	/**
	 * setSeedName
	 * Protected because is should only be called by the children class extending
	 * this one.
	 *
	 * @param string $name
	 * @return SeedCore
	 */
    protected function setSeedName($name)
    {
        $this->seedName = $name;
        return $this;
    }

    /**
     * getSeedName
     * Public because it can be called by LoadSeedsCommand or UnloadSeedsCommand.
     *
     * @return string
     */
    public function getSeedName(): string
    {
        return $this->seedName;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $name = $this->getSeedName();

        if (!$name) {
            throw new InvalidArgumentException('Please configure the command '.get_called_class().' with a seed name');
        }

        $this->setName($this->prefix.$this->separator.$this->seedName)
            ->addArgument('method', InputArgument::OPTIONAL);
    }

	/**
	 * execute
	 * Execute the seed method according to the method argument (load/unload).
	 *
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 *
	 * @return SeedCore
	 */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $method = $input->getArgument('method') ?: 'load';

        if (!in_array($method, ['load', 'unload'])) {
            throw new InvalidArgumentException('Method should be one of: load, unload');
        }

        if (!method_exists($this, $method)) {
            throw new InvalidArgumentException("Method '$method' does not exist in the command ".get_called_class());
        }

        return $this->$method($input, $output);
    }

	/**
	 * disableDoctrineLogging
	 * Shortcut to disable doctrine logging, useful when loading big seeds to
	 * avoid memory leaks.
	 *
	 * @return SeedCore
	 */
    protected function disableDoctrineLogging()
    {
        $this->container->get('doctrine')
            ->getConnection()
            ->getConfiguration()
            ->setSQLLogger(null);

        return $this;
    }
}
