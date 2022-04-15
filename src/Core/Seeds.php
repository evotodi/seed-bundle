<?php

namespace Evotodi\SeedBundle\Core;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Evotodi\SeedBundle\Model\AlterationExtensionInterface;
use Evotodi\SeedBundle\Model\ConfigurableExtensionInterface;

abstract class Seeds extends Command
{
	private array $extensions;

	/**
	 * __construct.
	 *
	 * @param array $extensions
	 */
    public function __construct( array $extensions = [])
    {
        $this->extensions = $extensions;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('seed:'.$this->getMethod())
            ->setDescription('Load requested seeds')
            ->addOption('break', '-b', InputOption::VALUE_NONE)
            ->addOption('debug', '-d', InputOption::VALUE_NONE)
            ->addOption('from', '-f', InputOption::VALUE_REQUIRED);
        $help = <<<EOT

This command loads/unloads a list of seeds

If you want to break on a bad exit code use -b

Want to debug seeds ordering? You can launch a simulation by using the -d option:

  <info>php app/console seeds:load -d</info>
EOT;

        foreach ($this->extensions as $extension) {
            if ($extension instanceof ConfigurableExtensionInterface) {
                $extension->configure($this);
                $help .= $extension->getHelp();
            }
        }

        $this->setHelp($help);
    }

    /**
     * This is wrapping every seed in a single command based on $this->method
     * it's also handling arguments and options to launch multiple seeds.
     * {@inheritdoc}
     *
     * @see LoadSeedsCommand
     * @see UnloadSeedsCommand
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $break = $input->getOption('break');
        $debug = $input->getOption('debug');
        $from = $input->getOption('from');

        $commands = $this->getSeedsCommands();

        foreach ($this->extensions as $extension) {
            if ($extension instanceof AlterationExtensionInterface) {
                $extension->apply($commands, $input);
            }
        }

        $l = count($commands);

        //No seeds? Stop.
        if ($l == 0) {
            $output->writeln('<info>No seeds</info>');
            return 1;
        }

        foreach ($this->extensions as $extension) {
            if (!($extension instanceof AlterationExtensionInterface)) {
                $extension->apply($commands, $input);
            }
        }

        //Prepare arguments
        $arguments = new ArrayInput(['method' => $this->getMethod()]);
        $returnCode = 0;
        $startFrom = true;

        if ($from) {
          $startFrom = false;
        }

        //Loop and execute every seed by printing tstart/tend

        for ($i = 0; $i < $l; ++$i) {

            $tstart = microtime(true);

            if (false === $startFrom) {
              if ($from !== $commands[$i]->getName()) {
                continue;
              } else {
                $startFrom = true;
              }
            }

            $output->writeln(sprintf(
                '<info>[%d] Starting %s</info>',
                $commands[$i]->getOrder(), $commands[$i]->getName()
            ));

            if ($debug) {
                $code = 0;
            } else {
                $code = $commands[$i]->run($arguments, $output);
            }

            $time = microtime(true) - $tstart;

            if ($code === 0) {
                $output->writeln(sprintf(
                    '<info>[%d] Seed %s done (+%d seconds)</info>',
                    $commands[$i]->getOrder(), $commands[$i]->getName(), $time
                ));

                continue;
            }

            $output->writeln(sprintf(
                '<error>[%d] Seed %s failed (+%d seconds)</error>',
                $commands[$i]->getOrder(), $commands[$i]->getName(), $time
            ));

            if ($break === true) {
                $returnCode = 1;
                break;
            }
        }
        return $returnCode;
    }

	/**
	 * Get seeds from app commands.
	 *
	 * @return array commands
	 */
    private function getSeedsCommands(): array
    {
        $app = $this->getApplication();
        $commands = [];

        //Get every command, if no seeds argument we take all available seeds
        foreach ($app->all() as $command) {
            //is this a seed?
            if ($command instanceof SeedCore) {
                $commands[] = $command;
            }
        }

        return $commands;
    }
}
