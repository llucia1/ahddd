<?php

namespace GridCP\Net\Ip4\Domain\Exceptions;




class Ip4NotFound extends \Exception
{
    public function __construct(String $ip4)
    {
        parent::__construct('Ip4 not found -> ' . $ip4);
    }

}