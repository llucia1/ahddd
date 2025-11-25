<?php

namespace GridCP\Net\Ip4\Domain\Exceptions;

use Error;

class Ip4Duplicated extends Error
{
    public function __construct(String $ip4)
    {
        parent::__construct('Ip4 Duplicated ->' . $ip4, 409);
    }

}