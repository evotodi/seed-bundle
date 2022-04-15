<?php

namespace Evotodi\SeedBundle\Command;

use Evotodi\SeedBundle\Core\Seeds;

final class UnloadSeedsCommand extends Seeds
{
	private string $method;

	protected function configure()
    {
        $this->method = 'unload';
        parent::configure();
    }

	public function getMethod(): string
    {
		return $this->method;
	}


}
