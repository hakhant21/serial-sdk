<?php

use Hakhant\Sdk\Dispenser\Connectors\SocketConnector;

beforeEach(function () {
    $this->connector = new SocketConnector('127.0.0.1', 1234);
});

it('can initialize the socket connector class', function () {
    expect($this->connector)->toBeInstanceOf(SocketConnector::class);
});


it('can send data to the socket server', function () {
    $this->connector->send(['0x30', '0x31', '0x70', '0x72', '0x65', '0x73', '0x65', '0x74'])
        ->then(function ($response) {
            expect($response)->toBe('01preset');
        }, function ($error) {
            expect($error)->toBeInstanceOf(Exception::class);
    });
});