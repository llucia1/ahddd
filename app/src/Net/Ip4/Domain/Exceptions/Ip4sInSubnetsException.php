<?php

namespace GridCP\Net\Ip4\Domain\Exceptions;

use Exception;

class Ip4sInSubnetsException extends Exception
{
    public function __construct(array $ip4)
    {
        parent::__construct('The following Ips are on subnet. Cannot be changed -> ' . implode(', ', $ip4) , 409);
    }

}