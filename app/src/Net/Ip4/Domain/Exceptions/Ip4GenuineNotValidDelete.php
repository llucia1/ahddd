<?php

namespace GridCP\Net\Ip4\Domain\Exceptions;




class Ip4GenuineNotValidDelete extends \Exception
{
    public function __construct(String $ip4)
    {
        parent::__construct('It is a genuine IP, not valid for deletion -> ' . $ip4);
    }

}