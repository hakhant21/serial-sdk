<?php

namespace Hakhant\Sdk\Dispenser\Protocols;

use Hakhant\Sdk\Dispenser\Contracts\ConnectorInterface;

class RedStar
{
    protected $connector;
    public function __construct($connector)
    {
        $this->connector = $connector;
    }

    public function sendCommand(array $command): string
    {
        $command = $this->buildCommand($command);
        $response = $this->connector->send($command);
        return $this->readResponse($response);
    }

    private function buildCommand(array $command): array
    {
        // Calculate the CRC16 checksum for the command
        $crc = $this->calculateCRC($command);
    
        // Append the CRC16 checksum (low byte first, then high byte)
        $lowByte = $crc & 0xFF;        // Low byte of CRC16
        $highByte = ($crc >> 8) & 0xFF; // High byte of CRC16
    
        // Merge the original command array with the CRC16 bytes
        $commandWithCRC = array_merge($command, [$lowByte, $highByte]);
    
        return $commandWithCRC;
    }


    private function calculateCRC(array $data): int
    {
        $crc = 0xFFFF;
        $polynomial = 0xA001;
        foreach ($data as $byte) {
            // Ensure each element is treated as a byte (in case it's a string representation)
            $byte = is_int($byte) ? $byte : ord($byte);
    
            $crc ^= $byte;  // XOR byte into the CRC value
    
            // Process each bit of the byte
            for ($i = 0; $i < 8; $i++) {
                if ($crc & 0x0001) {
                    $crc = ($crc >> 1) ^ $polynomial;  // Shift and XOR with polynomial
                } else {
                    $crc >>= 1;  // Just shift
                }
            }
        }
    
        // Ensure the result is a 16-bit value
        return $crc & 0xFFFF;
    }

    private function readResponse(string $response): string
    {
        $dataWithoutCRC = substr($response, 0, -2);  // All bytes except the last 2 (CRC bytes)

        // Step 2: Extract the received CRC from the last two bytes of the response
        $receivedCRC = substr($response, -2);  // The last two bytes are assumed to be the received CRC

        // Step 3: Calculate the CRC for the data (without the CRC part)
        $calculatedCRC = $this->calculateCRC(str_split($dataWithoutCRC));  // Call to the CRC calculation function

        // Step 4: Split the calculated CRC into two bytes (low byte and high byte)
        $crcLow = chr($calculatedCRC & 0xFF);         // Low byte of the CRC (last 8 bits)
        $crcHigh = chr(($calculatedCRC >> 8) & 0xFF); // High byte of the CRC (next 8 bits)

        // Step 5: Compare the received CRC with the calculated CRC
        if ($receivedCRC === $crcLow . $crcHigh) {
            // If the CRC matches, the response is valid
            return bin2hex($response);
        } else {
            // If the CRC does not match, the response is invalid
            echo "Invalid CRC in response.\n";
        }
    }
}