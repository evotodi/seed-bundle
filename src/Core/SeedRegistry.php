<?php

namespace Evotodi\SeedBundle\Core;

use JetBrains\PhpStorm\ArrayShape;
use Webmozart\Glob\Glob;

class SeedRegistry
{
    #[ArrayShape(['name' => 'string', 'class' => 'object', 'order' => 'int', 'path' => 'string'])]
    private array $seeds = [];

    /** @noinspection PhpUnused */
    public function addSeed(string $seedName, object $seedClass, int $seedOrder): void
    {
        $this->seeds[strtolower($seedName)] = [
            'name' => strtolower($seedName),
            'class' => $seedClass,
            'order' => $seedOrder,
            'path' => "/". str_replace(':', "/", strtolower($seedName)),
        ];
    }

    #[ArrayShape(['name' => ['name' => 'string', 'class' => 'object', 'order' => 'int', 'path' => 'string']])]
    public function all(): array
    {
        return $this->seeds;
    }

    #[ArrayShape(['name' => 'string', 'class' => 'object', 'order' => 'int',  'path' => 'string'])]
    public function get(string $seedName): ?array
    {
        if(key_exists(strtolower($seedName), $this->seeds)){
            return $this->seeds[strtolower($seedName)];
        }
        return null;
    }

    public function has(string $seedName): bool
    {
        if(key_exists(strtolower($seedName), $this->seeds)){
            return true;
        }
        return false;
    }

    #[ArrayShape(['name' => 'string'])]
    public function keys(): array
    {
        return array_keys($this->seeds);
    }

    #[ArrayShape(['name' => 'string'])]
    public function glob(string $glob): array
    {
        $paths = array_column($this->seeds, 'path');

        $globPath = strtolower($glob);
        if(!str_starts_with($glob, '/')){
            $globPath = '/' . $glob;
        }
        $globPath = str_replace(':', '/', $globPath);

        $matches = Glob::filter($paths, $globPath);

        $ret = [];
        foreach ($matches as $val){
            $seed = $this->get(ltrim(str_replace('/', ":", $val), ':'));
            if($seed){
                $ret[] = $seed['name'];
            }
        }
        if(empty($ret)){
            $ret[] = strtolower($glob);
        }
        return $ret;
    }
}
