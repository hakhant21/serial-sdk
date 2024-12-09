<?php

namespace Hakhant\Sdk\Dispenser\Protocols;

use Hakhant\Sdk\Dispenser\Contracts\ConnectorInterface;

class RedStar 
{
    protected ConnectorInterface $connector;
    public function __construct(ConnectorInterface $connector)
    {
        $this->connector = $connector;
    }

    public function sendCommand(array $command): string
    {
        $command = $this->buildCommand($command);
        $response = $this->connector->send($command);
        return $this->parseResponse($response);
    }

    private function buildCommand(array $command): array
    {
        $crc = $this->calculateCRC($command);
        return array_merge($command, [$crc & 0xFF, ($crc >> 8) & 0xFF]);
    }

    private function calculateCRC(array $data): int
    {
        $crc = 0xFFFF;
        foreach ($data as $byte) {
            $crc ^= ($byte << 8);
            for ($i = 0; $i < 8; $i++) {
                $crc = ($crc & 0x8000) ? ($crc << 1) ^ 0x1021 : ($crc << 1);
            }
        }
        return $crc & 0xFFFF;
    }

    private function parseResponse(string $response): string
    {
        return bin2hex($response);
    }
}