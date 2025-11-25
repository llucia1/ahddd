<?php

namespace GridCP\Net\Ip4\Domain\Exceptions;

use Exception;

class NerworkNoExistException extends Exception
{
    public function __construct()
    {
        parent::__construct('Network does not exist.');
    }

}