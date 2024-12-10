<?php

namespace Hakhant\Sdk\Dispenser\Contracts;

interface ConnectorInterface
{
     public function send(array $data);
}