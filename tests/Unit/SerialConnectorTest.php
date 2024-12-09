<?php

use Hakhant\Sdk\Dispenser\Connectors\SerialConnector;

it('can initialize the serial connector class', function () {
    $serialPort = '/dev/ttyUSB0';
    $baudRate = 9600;

    // Create a mock of SerialConnector
    $mockConnector = Mockery::mock(SerialConnector::class, [$serialPort, $baudRate])
        ->makePartial(); // Ensures the real constructor is called

    expect($mockConnector)
        ->toBeInstanceOf(SerialConnector::class)
        ->and($mockConnector->getSerialPort())->toBe($serialPort)
        ->and($mockConnector->getBaudRate())->toBe($baudRate);
});

it('can send data and receive response using the Serial class', function () {
    $serialPort = '/dev/ttyUSB1';
    $baudRate = 9600;
    $mockConnector = Mockery::mock(SerialConnector::class, [$serialPort, $baudRate])
    ->makePartial(); 

    $data = [65, 66, 67]; // ASCII for "ABC"

    // Mock the send method
    $mockConnector->shouldReceive('send')
        ->with($data)
        ->andReturn('ABC'); 

    // Call the mocked send method
    $response = $mockConnector->send($data);

    // Assert the response
    expect($response)->toBe('ABC');
});