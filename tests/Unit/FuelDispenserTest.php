<?php

use Hakhant\Sdk\Dispenser\FuelDispenser;
use Hakhant\Sdk\Dispenser\Protocols\RedStar;

beforeEach(function () {
    $this->sdk = Mockery::mock(FuelDispenser::class, [
        'brand' => 'redstar',
        'config' => [
            'type' => 'serial',
            'serialPort' => '/dev/ttyUSB0',
            'baudRate' => 9600
        ]
    ]);
});

afterEach(function () {
    Mockery::close();
});

it('can initialize a Fuel Dispenser class', function () {
    expect($this->sdk)->toBeInstanceOf(FuelDispenser::class);
});

it('sends a command using Fuel Dispenser and receives a response', function () {
    $this->sdk->shouldReceive('sendCommand')->andReturn('01preset');

    $response = $this->sdk->sendCommand(['0x30', '0x31', '0x70', '0x72', '0x65', '0x73', '0x65', '0x74']);
    
    expect($response)->toBe('01preset');
});
