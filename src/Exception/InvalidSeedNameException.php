<?php

namespace Evotodi\SeedBundle\Exception;

use Evotodi\SeedBundle\Core\SeedCoreCommand;
use Exception;

class InvalidSeedNameException extends Exception
{
    public const MESSAGE = "Invalid seed name! Seed name must not be 'load', 'unload', or '" . SeedCoreCommand::CORE_SEED_NAME . "'. You may have not added 'public static function seedName(){ return \"my_seed_name\"; }' to your seed class ";
    protected $message = self::MESSAGE;
}
