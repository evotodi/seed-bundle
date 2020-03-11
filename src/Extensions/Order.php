<?php

namespace Evotodi\SeedBundle\Extensions;

use Symfony\Component\Console\Input\InputInterface;
use Evotodi\SeedBundle\Model\SeedExtensionInterface;
use Evotodi\SeedBundle\Core\SeedCore;

class Order implements SeedExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function apply(array &$commands, InputInterface $input)
    {
        //Sort through getOrder
        usort($commands, function (SeedCore $a, SeedCore $b) {
            return $a->getOrder() - $b->getOrder();
        });
    }
}
