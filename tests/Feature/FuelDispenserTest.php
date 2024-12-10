<?php

use Hakhant\Sdk\Dispenser\FuelDispenser;
use Hakhant\Sdk\Dispenser\Protocols\RedStar;


it('sends a command using FuelDispenser and receives a response', function () {
    
    $sdk = Mockery::mock(FuelDispenser::class, [
        'brand' => 'redstar',
        'config' => [
            'type' => 'serial',
            'serialPort' => '/dev/ttyUSB0',
            'baudRate' => 9600
        ]
    ]);
    
    $sdk->shouldReceive('sendCommand')->andReturn('01preset');

    $response = $sdk->sendCommand(['0x30', '0x31', '0x70', '0x72', '0x65', '0x73', '0x65', '0x74']);
    
    expect($response)->toBe('01preset');
});
