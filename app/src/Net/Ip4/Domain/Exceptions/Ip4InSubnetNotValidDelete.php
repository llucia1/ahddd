<?php

namespace GridCP\Net\Ip4\Domain\Exceptions;




class Ip4InSubnetNotValidDelete extends \Exception
{
    public function __construct(String $ip4)
    {
        parent::__construct('IP is a subnet, not valid for deletion -> ' . $ip4);
    }

}