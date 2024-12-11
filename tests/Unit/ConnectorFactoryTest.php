<?php

use Hakhant\Sdk\Dispenser\Connectors\SerialConnector;
use Hakhant\Sdk\Dispenser\Factories\ConnectorFactory;


it('can create connector using the ConnectorFactory', function () {
    $connector = ConnectorFactory::getConnector('serial', [
        'serialPort' => '/dev/ttyUSB0',
        'baudRate' => 9600,
    ]);

    expect($connector)->toBeInstanceOf(SerialConnector::class);
});

it('can throw an exception if the connector type is not supported', function () {
    $connector = ConnectorFactory::getConnector('unknown', [
        'serialPort' => '/dev/ttyUSB0',
        'baudRate' => 9600,
    ]);
})->throws(InvalidArgumentException::class);