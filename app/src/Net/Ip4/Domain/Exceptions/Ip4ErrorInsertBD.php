<?php

namespace GridCP\Net\Ip4\Domain\Exceptions;

use Error;

class Ip4ErrorInsertBD extends Error
{
    public function __construct(String $ip4, string $msnError)
    {
        parent::__construct('Error insert ip in DB' . $ip4 . ' -> '.$msnError, 409);
    }

}