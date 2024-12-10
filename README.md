
# Serial Communication SDK

### The Serial Communication SDK is a PHP package designed for communicating with devices over serial ports. It simplifies the process of sending commands and receiving responses using phpSerial

### Installation

```bash

composer require hakhant/serial-sdk

```

### Usage 

```php

$config = [
  'brand'=> 'redstar',
  'config' => [
        'type' => 'serial',
        'serialPort' => '/dev/ttyUSB0',
        'baudRate' => 9600
   ]
]

$dispenser = new FuelDispenser($config);
// 01preset
$response = $dispenser->sendCommand(['0x30', '0x31', '0x70', '0x72', '0x65', '0x73', '0x65', '0x74']); 

echo $response;
```
