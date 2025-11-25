<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4FloatGroup\Domain\Service;

use GridCP\Net\Ip4FloatGroup\Application\Responses\FloatGroupResponse;

use GridCP\Net\Ip4FloatGroup\Domain\VO\Ip4FloatGroupsUuid;

interface IDisableFloatGroup
{
    function disableFloatGroup(Ip4FloatGroupsUuid $ip4FloatGroup):void;

}