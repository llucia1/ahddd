<?php

namespace GridCP\Net\Ip4FloatGroup\Domain\Exception;

class ErrorUuidInvalid extends \Exception
{
    public function __construct(String $uuid)
    {
        parent::__construct('Error Uuid Invalid -> '.$uuid);
    }

}