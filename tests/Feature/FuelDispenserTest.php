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
    
    $sdk->shouldReceive('sendCommand')->andReturn('01020304');

    expect($sdk->sendCommand(['01', '02', '03', '04']))->toBe('01020304');
});
