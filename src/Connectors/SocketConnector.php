<?php

namespace Hakhant\Sdk\Dispenser\Connectors;

use Exception;
use RuntimeException;
use React\EventLoop\Loop;
use React\Promise\Deferred;
use React\Socket\Connector;
use Hakhant\Sdk\Dispenser\Contracts\ConnectorInterface;

class SocketConnector implements ConnectorInterface
{
    protected string $host;
    protected int $port;

    public function __construct(string $host, int $port)
    {
        $this->host = $host;
        $this->port = $port;
    }

    public function send(array $data)
    {
        $loop = Loop::get();
        $connector = new Connector($loop);
        $deferred = new Deferred();
        
        $connector->connect("tcp://" . $this->host . ":" . $this->port)
            ->then(function ($connection) use ($data, $deferred) {
                $packData = implode(' ', array_map('chr', array_map('hexdec', $data)));
                $connection->write($packData);

                $connection->on('data', function ($response) use ($connection, $deferred) {
                    // return response to the callback function
                    $deferred->resolve($response);

                    $connection->close();
                });

                // Handle connection close
                $connection->on('close', function () use ($deferred, $connection) {
                    $connection->close();
                    $deferred->reject(new Exception("Connection closed"));
                });
            })->otherwise(function ($error) use ($deferred) {
                $deferred->reject($error);
            });

            $loop->run();

            return $deferred->promise();
    }


    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): int
    {
        return $this->port;
    }
}
