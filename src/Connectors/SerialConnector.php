<?php

namespace Hakhant\Sdk\Dispenser\Connectors;

use RuntimeException;
use Hakhant\Sdk\Dispenser\Serials\Serial;
use Hakhant\Sdk\Dispenser\Contracts\ConnectorInterface;

class SerialConnector implements ConnectorInterface
{
    private $serialPort;
    private $baudRate;
    private $serial;

    public function __construct(string $serialPort, int $baudRate)
    {
        $this->serialPort = $serialPort;
        $this->baudRate = $baudRate;
        $this->serial = $this->initializeSerial();
    }

    public function send(array $data): string
    {
        $this->configureSerialPort();

        $packedData = $this->packData($data);

        $this->serial->sendMessage($packedData);

        $response = $this->serial->readPort();

        $this->serial->deviceClose();

        return $response;
    }

    protected function initializeSerial(): Serial
    {
        return new Serial();
    }

    protected function configureSerialPort(): void
    {
        $this->validateSerialPort(); 
        $this->serial->deviceSet($this->serialPort);
        $this->serial->confBaudRate($this->baudRate);
        $this->serial->confParity('none');
        $this->serial->confCharacterLength(8);
        $this->serial->confStopBits(1);
        $this->serial->confFlowControl('none');
        $this->serial->deviceOpen();
    }

    protected function validateSerialPort(): void
    {
        if(!$this->serial->deviceSet($this->serialPort)) {
            throw new RuntimeException("Failed to set serial port: {$this->serialPort}");
        }  
    }

    protected function packData(array $hexData): string
    {
        return implode('', array_map(function($hex) {
            return chr(hexdec(str_replace('0x', '', $hex)));
        }, $hexData));
    }

    public function getSerialPort(): string
    {
        return $this->serialPort;
    }

    public function getBaudRate(): int
    {
        return $this->baudRate;
    }
}