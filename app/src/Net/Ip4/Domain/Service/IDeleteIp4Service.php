<?php

namespace GridCP\Net\Ip4\Domain\Service;


use GridCP\Net\Ip4\Domain\VO\Ip4Ip;
use GridCP\Net\Ip4\Domain\VO\Ip4Ips;

interface IDeleteIp4Service
{
    public function deleteIP4s(Ip4Ips $ip4s): ?array;
}