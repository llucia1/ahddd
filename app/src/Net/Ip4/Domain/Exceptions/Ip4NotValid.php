<?php

namespace GridCP\Net\Ip4\Domain\Exceptions;




class Ip4NotValid extends \Exception
{
    public function __construct(String $ip4)
    {
        parent::__construct('Ip4 not valid -> ' . $ip4);
    }

}