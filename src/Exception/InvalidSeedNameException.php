<?php

namespace Evotodi\SeedBundle\Exception;

use Exception;

class InvalidSeedNameException extends Exception
{
    protected $message = "Invalid seed name. Must not bo load or unload";
}
