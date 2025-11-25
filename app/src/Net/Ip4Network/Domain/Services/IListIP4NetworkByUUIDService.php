<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Network\Domain\Services;

use GridCP\Net\Ip4Network\Application\Responses\Ip4NetworkResponse;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4NetworkEntity;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkUUID;

interface IListIP4NetworkByUUIDService
{
    public function getAll(Ip4NetworkUUID $uuid): Ip4NetworkResponse;
    function toResponse(Ip4NetworkEntity $ip4NetworkEntity):Ip4NetworkResponse;
}