<?php

namespace GridCP\Net\Ip4\Domain\Exceptions;

use Exception;

class Ip4AreAsignnedException extends Exception
{
    public function __construct(array $ip4)
    {
        parent::__construct('The following Ips are assigned ( Genuine Ip ). Cannot be changed -> ' . implode(', ', $ip4) , 409);
    }

}