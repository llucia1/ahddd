<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Network\Domain\Services;

use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4FloatGroupEntity;
use GridCP\Net\Ip4Network\Domain\VO\FloatGroupUuuid;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkUUID;

interface IAssociateNetworkFloatGroup
{
    public function associate(Ip4NetworkUUID $networkUuid, FloatGroupUuuid $floatGroupUuid):void;
    public function getFloatGroupByUuid(FloatGroupUuuid $floatGroupUuid): ?Ip4FloatGroupEntity;
}