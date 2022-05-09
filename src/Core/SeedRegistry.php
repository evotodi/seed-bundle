<?php

namespace Evotodi\SeedBundle\Core;

use JetBrains\PhpStorm\ArrayShape;

class SeedRegistry
{
    #[ArrayShape(['name' => 'string', 'class' => 'object', 'order' => 'int'])]
    private array $seeds = [];

    /** @noinspection PhpUnused */
    public function addSeed(string $seedName, object $seedClass, int $seedOrder): void
    {
        $this->seeds[$seedName] = [
            'name' => $seedName,
            'class' => $seedClass,
            'order' => $seedOrder,
        ];
    }

    #[ArrayShape(['name' => 'string', 'class' => 'object', 'order' => 'int'])]
    public function all(): array
    {
        return $this->seeds;
    }

    #[ArrayShape(['name' => 'string', 'class' => 'object', 'order' => 'int'])]
    public function get(string $seedName): ?array
    {
        if(key_exists($seedName, $this->seeds)){
            return $this->seeds[$seedName];
        }
        return null;
    }

    public function has(string $seedName): bool
    {
        if(key_exists($seedName, $this->seeds)){
            return true;
        }
        return false;
    }

    public function keys(): array
    {
        return array_keys($this->seeds);
    }
}
