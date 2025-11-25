<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4FloatGroup\Domain\Service;

use GridCP\Net\Ip4FloatGroup\Domain\VO\Ip4FloatGroups;

interface ICreateIp4FloatGroup
{
    function createIpFloatGroup(Ip4FloatGroups $ip4FloatGroup):string;

}