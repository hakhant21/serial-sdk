<?php

namespace Hakhant\Sdk\Dispenser\Factories;

use InvalidArgumentException;
use Hakhant\Sdk\Dispenser\Connectors\SerialConnector;
class ConnectorFactory 
{
    public static function getConnector(string $type, array $config)
    {
        switch ($type) {
            case 'serial':
                return new SerialConnector($config['serialPort'], $config['baudRate']);
            default:
                throw new InvalidArgumentException("Unsupported connector type: " . $type);
        }
    }
}

