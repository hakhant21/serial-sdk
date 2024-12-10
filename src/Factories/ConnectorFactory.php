<?php

namespace Hakhant\Sdk\Dispenser\Factories;

use InvalidArgumentException;
use Hakhant\Sdk\Dispenser\Connectors\SerialConnector;
use Hakhant\Sdk\Dispenser\Connectors\SocketConnector;

class ConnectorFactory 
{
    public static function getConnector(string $type, array $config)
    {
        switch ($type) {
            case 'serial':
                return new SerialConnector($config['serialPort'], $config['baudRate']);
            case 'tcp':
                return new SocketConnector($config['host'], $config['port']);
            default:
                throw new InvalidArgumentException("Unsupported connector type: " . $type);
        }
    }
}

