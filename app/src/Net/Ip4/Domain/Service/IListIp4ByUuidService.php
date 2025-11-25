<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4\Domain\Service;

use GridCP\Net\Ip4\Application\Response\Ip4Response;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4Entity;
use GridCP\Net\Ip4\Domain\VO\Ip4Uuid;

interface IListIp4ByUuidService
{
    public function getByUuid(Ip4Uuid $uuid): Ip4Response;

    public function toResponse(Ip4Entity $ip4Entity): Ip4Response;

}