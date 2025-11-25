<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4FloatGroup\Domain\Service;

use GridCP\Net\Ip4FloatGroup\Application\Responses\FloatGroupResponse;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4FloatGroupEntity;

interface IGetFloatGroupByUUIDService
{
    public function getAll(string $uuid): FloatGroupResponse;
    public function toResponse(Ip4FloatGroupEntity $FloatGroup):FloatGroupResponse;


}