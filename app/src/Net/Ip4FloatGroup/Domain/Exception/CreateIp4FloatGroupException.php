<?php

namespace GridCP\Net\Ip4FloatGroup\Domain\Exception;

use Error;

class CreateIp4FloatGroupException extends Error
{
    public function __construct(Error $e)
    {
        parent::__construct(sprintf('Error creating IP4 Float Group', $e->getMessage()));
    }

}