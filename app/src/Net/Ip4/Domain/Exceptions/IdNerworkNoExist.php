<?php

namespace GridCP\Net\Ip4\Domain\Exceptions;

use Error;

class IdNerworkNoExist extends Error
{
    public function __construct(string $idNetwork)
    {
        parent::__construct('The id_network does not exist, id_network: ' . $idNetwork);
    }

}