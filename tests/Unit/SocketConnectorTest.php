<?php

use Hakhant\Sdk\Dispenser\Connectors\SocketConnector;

it('can connect to a socket server', function () {
    $connector = new SocketConnector('127.0.0.1', 1234);

    $connector->send(['0x30', '0x31', '0x70', '0x72', '0x65', '0x73', '0x65', '0x74'])
        ->then(function ($response) {
            expect($response)->toBe('01preset');
        }, function ($error) {
            expect($error)->toBeInstanceOf(Exception::class);
        });
});