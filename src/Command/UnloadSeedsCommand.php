<?php

namespace Evotodi\SeedBundle\Command;

use Evotodi\SeedBundle\Core\Seeds;

final class UnloadSeedsCommand extends Seeds
{
	private $method;

	protected function configure()
    {
        $this->method = 'unload';
        parent::configure();
    }

	public function getMethod()
	{
		return $this->method;
	}


}
