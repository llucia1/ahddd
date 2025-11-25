<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Tag\Domain\Service;

use GridCP\Net\Ip4Tag\Domain\VO\Ip4Tag;

interface ICreateIp4Tag
{
    function createIp4Tag(Ip4Tag $ip4Tag):string;

}