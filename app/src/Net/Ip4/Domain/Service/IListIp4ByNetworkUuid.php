<?php
namespace GridCP\Net\Ip4\Domain\Service;


use GridCP\Net\Ip4\Application\Response\Ip4sResponse;
use GridCP\Net\Ip4\Domain\VO\Ip4UuidNetwork;

interface IListIp4ByNetworkUuid
{
    public function getAllByNetworkUuid(Ip4UuidNetwork $networkUuid):Ip4sResponse;

    public function toResponse(): callable;

}