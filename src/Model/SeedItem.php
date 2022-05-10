<?php

namespace Evotodi\SeedBundle\Model;

class SeedItem
{
    public string $name;
    public object $classRef;
    public int $order;
    public bool $manual;

    public function __construct(string $name, object $classRef, int $order, bool $manual = false)
    {
        $this->name = $name;
        $this->classRef = $classRef;
        $this->order = $order;
        $this->manual = $manual;
    }

    public function getClassName(): string
    {
        return get_class($this->classRef);
    }
}
