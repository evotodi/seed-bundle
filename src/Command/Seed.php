<?php

namespace Evotodi\SeedBundle\Command;

use Evotodi\SeedBundle\Core\SeedCore;
use Evotodi\SeedBundle\Model\SeedInterface;

abstract class Seed extends SeedCore implements SeedInterface
{
    public function getOrder(): int
    {
        return 0;
    }
}
