<?php

namespace Hakhant\Sdk\Dispenser\Connectors;

use Exception;
use RuntimeException;
use React\EventLoop\Loop;
use React\Promise\Deferred;
use React\Socket\Connector;
use React\Promise\PromiseInterface;
use Hakhant\Sdk\Dispenser\Contracts\ConnectorInterface;

class SocketConnector implements ConnectorInterface
{
    protected string $host;
    protected int $port;
    protected int $timeout;
    protected Connector $connector;
    protected $loop;

    public function __construct(string $host, int $port, int $timeout = 30)
    {
        $this->validateHost($host);
        $this->validatePort($port);
        
        $this->host = $host;
        $this->port = $port;
        $this->timeout = $timeout;
        
        $this->loop = Loop::get();
        $this->connector = new Connector([
            'timeout' => $this->timeout,
            'dns' => false
        ], $this->loop);
    }

    public function send(array $data): PromiseInterface
    {
        $deferred = new Deferred();
        
        $timer = $this->loop->addTimer($this->timeout, function () use ($deferred) {
            $deferred->reject(new Exception("Connection timed out after {$this->timeout} seconds"));
            $this->loop->stop();
        });

        $this->connector->connect("tcp://{$this->host}:{$this->port}")
            ->then(
                function ($connection) use ($data, $deferred, $timer) {
                    $packData = $this->packData($data);
                    $connection->write($packData);

                    $connection->on('data', function ($response) use ($connection, $deferred, $timer) {
                        $this->loop->cancelTimer($timer);
                        $deferred->resolve($response);
                        $connection->close();
                        $this->loop->stop();
                    });

                    $connection->on('error', function ($error) use ($connection, $deferred, $timer) {
                        $this->loop->cancelTimer($timer);
                        $connection->close();
                        $deferred->reject(new RuntimeException("Connection error: {$error->getMessage()}"));
                        $this->loop->stop();
                    });

                    $connection->on('close', function () use ($deferred, $timer) {
                        $this->loop->cancelTimer($timer);
                        if (!$deferred->promise()->getState()) {
                            $deferred->reject(new Exception("Connection closed unexpectedly"));
                            $this->loop->stop();
                        }
                    });
                },
                function ($error) use ($deferred, $timer) {
                    $this->loop->cancelTimer($timer);
                    $deferred->reject(new RuntimeException("Failed to connect: {$error->getMessage()}"));
                    $this->loop->stop();
                }
            );

        $this->loop->run();
        return $deferred->promise();
    }

    protected function packData(array $data): string
    {
        return implode('', array_map(function($byte) {
            return chr(hexdec(trim($byte)));
        }, $data));
    }

    protected function validateHost(string $host): void
    {
        if (!filter_var($host, FILTER_VALIDATE_IP) && !filter_var($host, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
            throw new RuntimeException("Invalid host provided: {$host}");
        }
    }

    protected function validatePort(int $port): void
    {
        if ($port < 1 || $port > 65535) {
            throw new RuntimeException("Invalid port number: {$port}");
        }
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
