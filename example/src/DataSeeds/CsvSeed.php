<?php

namespace App\DataSeeds;

use Evotodi\SeedBundle\Command\Seed;
use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;

class CsvSeed extends Seed
{
    private ParameterBagInterface $parameterBag;
    private Filesystem $filesystem;

    public function __construct(ParameterBagInterface $parameterBag, Filesystem $filesystem)
    {
        parent::__construct();
        $this->parameterBag = $parameterBag;
        $this->filesystem = $filesystem;
    }

    public static function seedName(): string
    {
        return 'csv';
    }

    /**
     * @throws Exception
     */
    public function load(InputInterface $input, OutputInterface $output): int
    {
        $csvColumns = ['id', 'name', 'data'];
        $csvData = [];
        for($i = 0; $i < 10; $i++){
            $csvData[] = [$i, 'MyName'.$i, random_int(0, 1000)];
        }

        $this->filesystem->mkdir($this->parameterBag->get('kernel.project_dir').'/var');

        $file = fopen($this->parameterBag->get('kernel.project_dir').'/var/test.csv', 'w');

        fputcsv($file, $csvColumns);
        foreach ($csvData as $datum){
            fputcsv($file, $datum);
        }

        fclose($file);

        return 0;
    }

    public function unload(InputInterface $input, OutputInterface $output): int
    {
        $this->filesystem->remove($this->parameterBag->get('kernel.project_dir').'/var/test.csv');
        return 0;
    }
}
