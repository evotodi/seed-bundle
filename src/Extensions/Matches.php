<?php

namespace Evotodi\SeedBundle\Extensions;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Evotodi\SeedBundle\Model\SeedExtensionInterface;
use Evotodi\SeedBundle\Model\AlterationExtensionInterface;
use Evotodi\SeedBundle\Model\ConfigurableExtensionInterface;

class Matches implements SeedExtensionInterface, AlterationExtensionInterface, ConfigurableExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function apply(array &$commands, InputInterface $input)
    {
        $seeds = $input->getArgument('seeds');

        if (!$seeds) {
            return;
        }

        //Lowercase seeds names
        $seeds = array_map(function ($v) {
            return strtolower($v);
        }, $seeds);


        foreach ($commands as $key => $command) {
            if (!$this->match($command->getSeedName(), $seeds)) {
                unset($commands[$key]);
            }
        }
    }

    /**
     * Tests if a seed name is in the seeds array.
     */
    private function match(string $name, array $seeds): bool
    {
        foreach ($seeds as $choice) {
            if ($choice === $name) {
                return true;
            }

            $choice = '/'.str_replace('*', '.+', $choice).'/';

            if (preg_match($choice, $name)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function configure(Command $command)
    {
        $command
            ->addArgument('seeds', InputArgument::IS_ARRAY | InputArgument::OPTIONAL);
    }

    /**
     * {@inheritdoc}
     */
    public function getHelp(): string
    {
        return <<<EOT
You can specify seeds:

  <info>php app/console seeds:load Country Town Geography</info>

Without arguments, all seeds are loaded :

  <info>php app/console seeds:load</info>

EOT;
    }
}
