<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Tag\Domain\Service;

use GridCP\Net\Ip4Tag\Application\Response\Ip4TagResponse;
use GridCP\Net\Ip4Tag\Domain\VO\Ip4TagUuid;

interface IGetIp4Tag
{
    public function getIp4Tag(Ip4TagUuid $ip4Tag):Ip4TagResponse;

}