<?php

namespace Evotodi\SeedBundle\Core;

use ReflectionClass;
use ReflectionException;
use Symfony\Component\Console\Application;
use Symfony\Component\Finder\Finder;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Loader extends Container
{
    use ContainerAwareTrait;

	/**
	 * @throws ReflectionException
	 */
	public function loadSeeds(Application $application)
    {
        //add seed:load and seed:unload commands
        $application->add($this->container->get('seed.load_seeds_command'));
        $application->add($this->container->get('seed.unload_seeds_command'));

		$seedDir = sprintf('%s/%s',
			$this->container->getParameter('kernel.project_dir'),
			$this->container->getParameter('seed.directory')
		);

	    $finder = new Finder();
	    $finder->files()->name('*Seed.php')->in($seedDir);
	    foreach ($finder as $file){
	    	$className = $file->getFilenameWithoutExtension();

		    $alias = 'seed.command.'.strtolower($className);
		    if ($this->container->has($alias)) {
			    continue;
		    }

	    	$r = new ReflectionClass(sprintf('%s\%s', $this->container->getParameter('seed.namespace'), $className));
		    if ($r->getParentClass()->getName() == 'Evotodi\SeedBundle\Command\Seed' && !$r->isAbstract() && ($r->hasMethod('load') || $r->hasMethod('unload'))) {
                /** @noinspection PhpParamsInspection */
                $application->add(
				    $r->newInstanceArgs([$this->container->get('doctrine')])
			    );
		    }
	    }
    }
}
