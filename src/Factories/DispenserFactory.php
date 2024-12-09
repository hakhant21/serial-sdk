<?php

namespace Hakhant\Sdk\Dispenser\Factories;

use Hakhant\Sdk\Dispenser\Protocols\RedStar;

class DispenserFactory
{
    public static function getDispenser(string $brand, $connector)
    {
        switch ($brand) {
            case 'redstar':
                return new RedStar($connector);
            default:
                throw new \InvalidArgumentException("Unsupported dispenser brand: " . $brand);
        }
    }
}