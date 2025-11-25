<?php
namespace GridCP\Net\Ip4\Domain\Service;


use GridCP\Net\Ip4\Application\Response\Ip4sResponse;
use GridCP\Net\Ip4\Domain\VO\Ip4UuidNetwork;

interface IDeleteAllIp4OfNetwork
{
    public function deleteAllIp4OfNetwork(Ip4UuidNetwork $networkUuid):void;


}