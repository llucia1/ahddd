<?php

namespace GridCP\Net\Ip4\Domain\Exceptions;

use Error;

class NerworkNoExist extends \Exception
{
    public function __construct(string $idNetwork)
    {
        parent::__construct('The Network does not exist, uuid_network: ' . $idNetwork);
    }

}