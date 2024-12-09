<?php

namespace Hakhant\Sdk\Dispenser;

use Hakhant\Sdk\Dispenser\Factories\ConnectorFactory;
use Hakhant\Sdk\Dispenser\Factories\DispenserFactory;

class FuelDispenser 
{
    protected $dispenser;

    public function __construct(string $brand, array $config)
    {
        // Get the correct connector using the ConnectorFactory
        $connector = ConnectorFactory::getConnector($config['type'], $config);

        // Get the correct dispenser using the FuelDispenserFactory
        $this->dispenser = DispenserFactory::getDispenser($brand, $connector);
    }

    public function sendCommand(array $command): string
    {
        return $this->dispenser->sendCommand($command);
    }
}

