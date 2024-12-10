<?php

namespace Hakhant\Sdk\Dispenser\Connectors;

use RuntimeException;
use Hakhant\Sdk\Dispenser\Serials\Serial;
use Hakhant\Sdk\Dispenser\Contracts\ConnectorInterface;

class SerialConnector implements ConnectorInterface
{
    protected string $serialPort;
    protected int $baudRate;
    public function __construct(string $serialPort, int $baudRate)
    {
        $this->serialPort = $serialPort;
        $this->baudRate = $baudRate;
    }

    public function send(array $data): string
    {
        $handle = $this->openSerialPort();

        if (!$handle) {
            throw new RuntimeException("Unable to open serial port: " . $this->serialPort);
        }

        $serial = new Serial($this->serialPort, $this->baudRate);

        $serial->deviceSet($this->serialPort);

        $serial->confBaudRate($this->baudRate);
        
        $serial->confParity("none");
        
        $serial->confCharacterLength(8);
        
        $serial->confStopBits(1);
        
        $serial->confFlowControl("none");
        
        $serial->deviceOpen();

        $packData = implode('', array_map('chr', array_map('hexdec', $data)));

        $serial->sendMessage($packData);

        $response = $serial->readPort();

        $serial->deviceClose();

        return $response;
    }

    private function openSerialPort()
    {
        if (!file_exists($this->serialPort)) {
            throw new RuntimeException("Serial port not found: " . $this->serialPort);
        }

        $handle = @fopen($this->serialPort, 'w+b');
        if (!$handle) {
            return false;
        }

        // Configure serial port settings
        $this->configureSerialPort();

        return $handle;
    }

    private function configureSerialPort()
    {
        if (strncasecmp(PHP_OS, 'WIN', 3) === 0) {
            // Windows-specific configuration
            exec("mode COM1: BAUD={$this->baudRate} PARITY=N DATA=8 STOP=1");
        } else {
            // Unix-based configuration
            $command = sprintf('stty -F %s %d cs8 -cstopb -parenb', escapeshellarg($this->serialPort), $this->baudRate);
            exec($command);
        }
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