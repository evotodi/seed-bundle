<?php

namespace Evotodi\SeedBundle\Command;

use Evotodi\SeedBundle\Core\Seeds;

final class LoadSeedsCommand extends Seeds
{
	private string $method;

	protected function configure()
    {
        $this->method = 'load';
        parent::configure();
    }

	public function getMethod(): string
    {
		return $this->method;
	}


}
