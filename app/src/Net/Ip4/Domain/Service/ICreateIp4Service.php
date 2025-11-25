<?php

namespace GridCP\Net\Ip4\Domain\Service;


use GridCP\Net\Ip4\Domain\VO\Ip4;

interface ICreateIp4Service
{
    public function createIP4(Ip4 $ip4):array;
    public function toResponse(array $ip4sEntity):?array;

}