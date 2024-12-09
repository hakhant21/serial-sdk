<?php

use Hakhant\Sdk\Dispenser\Protocols\RedStar;
use Hakhant\Sdk\Dispenser\Factories\ConnectorFactory;
use Hakhant\Sdk\Dispenser\Factories\DispenserFactory;

it('can create a dispenser using the DispenserFactory', function () {
    $connector = Mockery::mock(ConnectorFactory::getConnector('serial', [
        'serialPort' => '/dev/ttyUSB0',
        'baudRate' => 9600,
    ]))->makePartial();

    $dispenser = Mockery::mock(DispenserFactory::getDispenser('redstar', $connector))
                ->makePartial();

    expect($dispenser)->toBeInstanceOf(RedStar::class);
});

it('can throw an exception if the dispenser brand is not supported', function () {
    $dispenser = $connector = Mockery::mock(ConnectorFactory::getConnector('serial', [
        'serialPort' => '/dev/ttyUSB0',
        'baudRate' => 9600,
    ]))->makePartial();

    $dispenser = Mockery::mock(DispenserFactory::getDispenser('unknown', $connector))
                ->makePartial(); 
})->throws(InvalidArgumentException::class);