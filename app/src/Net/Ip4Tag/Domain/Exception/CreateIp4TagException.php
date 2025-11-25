<?php

namespace GridCP\Net\Ip4Tag\Domain\Exception;

use Exception;

class CreateIp4TagException extends Exception
{
    public function __construct($message)
    {
        parent::__construct(sprintf('Error creating IP4 Tag: %s', $message));
    }
}