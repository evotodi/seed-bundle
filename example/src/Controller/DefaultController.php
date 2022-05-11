<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
	/**
	 * @Route("/", name="app_home")
	 * @return Response
	 */
    public function index(): Response
    {
//    	$reader = new LogReader($this->getParameter('kernel.logs_dir').'/dev.log', 0);
//    	$reader->getParser()->registerPattern('test', '/\[(?P<date>.*)\] (?P<logger>\w+).(?P<level>\w+): (?P<message>.*)(?P<context>.*)(?P<extra>.*)/');
//    	$reader->setPattern('test');
//	    foreach ($reader as $log) {
//		    echo sprintf("::: %s %s %s <br>", $log['date']->format('Y-m-d h:i:s'), $log['level'], $log['message']);
//	    }
//    	dd($reader);
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }
}
