<?php
declare(strict_types=1);

namespace GridCP\Device\Domain\Service;

use GridCP\Device\Domain\VO\Device;

interface IPostDeviceService
{
    function create(Device $device): string;

}