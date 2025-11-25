<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4FloatGroup\Domain\Service;

use GridCP\Net\Ip4FloatGroup\Domain\VO\Ip4FloatGroupsPacth;

interface IPatchFloatGroupService
{
    public function update(Ip4FloatGroupsPacth $floatGroup):void;
}