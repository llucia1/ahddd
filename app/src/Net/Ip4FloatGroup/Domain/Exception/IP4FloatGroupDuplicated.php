<?php

namespace GridCP\Net\Ip4FloatGroup\Domain\Exception;

class IP4FloatGroupDuplicated extends \Exception
{
public function __construct(String $ip4FloatGroup)
    {
        parent::__construct('Ip4 Float Group Duplicated ->'.$ip4FloatGroup);
    }

}