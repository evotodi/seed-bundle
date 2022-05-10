<?php

namespace Evotodi\SeedBundle\Core;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Evotodi\SeedBundle\Model\SeedItem;
use Exception;
use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SeedCoreCommand extends Command implements ContainerAwareInterface
{
    protected ?string $method = 'load';
    protected ?ContainerInterface $container;
    protected bool $manual = false;
    protected ManagerRegistry $manager;
    public const CORE_SEED_NAME = 'evo_core_seed';

    public static function seedName(): string
    {
        return self::CORE_SEED_NAME;
    }

    /** @noinspection PhpUnused */
    public static function getOrder(): int
    {
        return 0;
    }

    private function getSeedName(): string
    {
        return preg_replace('/^seed:/', '', $this->getName());
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        if(!is_null($container)){
            $this->manager = $container->get('doctrine');
        }
    }

    public function getManager(string $name = null): ObjectManager
    {
        return $this->manager->getManager($name);
    }

    protected function baseConfigure()
    {
        $this
            ->setName(sprintf('seed:%s', $this->seedName()))
            ->addOption('skip', '-s', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Seed(s) to skip')
            ->addOption('break', '-b', InputOption::VALUE_NONE, 'Stop on failed seed')
            ->addOption('debug', '-d', InputOption::VALUE_NONE, 'Debug seed ordering without making changes')
            ->addOption('from', '-f', InputOption::VALUE_REQUIRED, 'Start from seed')
        ;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('seeds', InputArgument::IS_ARRAY, 'seed(s)')
        ;
        $this->baseConfigure();
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $registry = $this->container->get('seed.registry');
        $debug = $input->getOption('debug');

        $seedLoadNames = [];
        if($input->hasArgument('seeds')){
            /** Add seeds from args to seedLoadNames */
            $seedLoadNames = $input->getArgument('seeds');
            if(empty($seedLoadNames)){
                $seedLoadNames = $registry->keys();
            }
        }else{
            /** Else add single command seed to seedLoadNames */
            $seedLoadNames[] = $this->getSeedName();
        }

        if($input->hasArgument('method')){
            $this->method = $input->getArgument('method');
            $this->manual = true;
        }

        if (!in_array($this->method, ['load', 'unload'])) {
            throw new InvalidArgumentException('Method should be one of: load, unload');
        }

        /** Check if seed from is valid */
        $from = $input->getOption('from');
        if($from) {
            if (!$registry->has($from)) {
                throw new InvalidArgumentException(sprintf("Invalid from seed %s! Valid seeds are '%s'", $from, join(", '", $registry->keys())));
            }
            $io->text(sprintf('Starting at seed %s', get_class($registry->get($from)['class'])));
        }

        $skips = $input->getOption('skip');
        if(!empty($skips)){
            foreach ($skips as $skip){
                if (!$registry->has($skip)) {
                    throw new InvalidArgumentException(sprintf("Invalid skip seed %s! Valid seeds are '%s'", $skip, join(", '", $registry->keys())));
                }
            }
        }

        /** Check if seed names are valid and create SeedItems */
        $seedItems = [];
        foreach ($seedLoadNames as $seedLoadName) {
            if (!$registry->has($seedLoadName)) {
                throw new InvalidArgumentException(sprintf("Invalid seed %s! Valid seeds are '%s'", $seedLoadName, join(", '", $registry->keys())));
            }
            $regItem = $registry->get($seedLoadName);
            $seedItems[] = new SeedItem($regItem['name'], $regItem['class'], $regItem['order'], $this->manual);
        }

        /** Sort the seeds in order */
        usort($seedItems, function ($a, $b) {
            return $a->order <=> $b->order;
        });

        /** Check for seeds to process */
        if(count($seedItems) == 0){
            $io->warning("No seed found");
            return Command::INVALID;
        }

        $processStartTime = microtime(true);
        $failedSeeds = [];
        $skippedSeeds = [];
        $startFrom = true;

        if($debug){
            $io->warning("Debugging seed order. No changes will be made.");
        }
        foreach ($seedItems as $seedItem){
            $retCode = 0;
            if($startFrom and $from){
                if($seedItem->name != $from){
                    $io->text(sprintf('<comment>Skipping by from %s order %s</comment>', $seedItem->getClassName(), $seedItem->order));
                    $skippedSeeds[] = $seedItem->getClassName();
                    continue;
                }else{
                    $startFrom = false;
                }
            }

            if(in_array($seedItem->name, $skips)){
                $io->text(sprintf('<comment>Skipping by skip %s order %s</comment>', $seedItem->getClassName(), $seedItem->order));
                $skippedSeeds[] = $seedItem->getClassName();
                continue;
            }

            $seedStartTime = microtime(true);

            switch ($this->method) {
                case 'load':
                    if($debug){
                        $io->text(sprintf('Debug loading %s with order %s', $seedItem->getClassName(), $seedItem->order));
                    }else {
                        $io->text(sprintf('Starting load %s order %s...', $seedItem->getClassName(), $seedItem->order));
                        if ($seedItem->manual) {
                            $retCode = $this->load($input, $output);
                        } else {
                            $cls = $seedItem->classRef;
                            $retCode = $cls->load($input, $output);

                        }
                    }
                    break;
                case 'unload':
                    if($debug){
                        $io->text(sprintf('Debug unloading %s with order %s', $seedItem->getClassName(), $seedItem->order));
                    }else {
                        $io->text(sprintf('Starting unload %s order %s...', $seedItem->getClassName(), $seedItem->order));
                        if ($seedItem->manual) {
                            $retCode = $this->unload($input, $output);
                        } else {
                            $cls = $seedItem->classRef;
                            $retCode = $cls->unload($input, $output);
                        }
                    }
                    break;
            }

            $seedTime = microtime(true) - $seedStartTime;

            if($retCode > 0){
                $io->text(sprintf('<error>Failed processing seed %s return code was %s (%.2f seconds)</error>', $seedItem->getClassName(), $retCode, $seedTime));
                $failedSeeds[] = $seedItem->getClassName();
            }else{
                if(!$debug) {
                    $io->text(sprintf('<info>Seed done %s (%.2f seconds)</info>', $seedItem->getClassName(), $seedTime));
                }
            }
            if($input->getOption('break')){
                $io->text("Stopped on failed seed");
                return Command::FAILURE;
            }
        }

        $processTime = microtime(true) - $processStartTime;
        if(!$debug) {
            $completeMessage = sprintf('Loading seed(s) completed (%.2f seconds)', $processTime);
            if(empty($failedSeeds) and empty($skippedSeeds)) {
                $io->success($completeMessage);
            }else{
                if(!empty($failedSeeds)){
                    $completeMessage .= sprintf("\nFailed Seeds: %s", join(', ', array_unique($failedSeeds)));
                }
                if(!empty($skippedSeeds)){
                    $completeMessage .= sprintf("\nSkipped Seeds: %s", join(', ', array_unique($skippedSeeds)));
                }
                $io->warning($completeMessage);
            }
        }

        return Command::SUCCESS;
    }

    /**
     * @throws Exception
     * @noinspection PhpUnusedParameterInspection
     */
    public function load(InputInterface $input, OutputInterface $output): int
    {
        throw new Exception("Core seed load should not be called");
    }

    /**
     * @throws Exception
     * @noinspection PhpUnusedParameterInspection
     */
    public function unload(InputInterface $input, OutputInterface $output): int
    {
        throw new Exception("Core seed unload should not be called");
    }

    /**
     * disableDoctrineLogging
     * Shortcut to disable doctrine logging, useful when loading big seeds to
     * avoid memory leaks.
     */
    protected function disableDoctrineLogging()
    {
        $this->manager
            ->getConnection()
            ->getConfiguration()
            ->setSQLLogger(null);
    }
}
