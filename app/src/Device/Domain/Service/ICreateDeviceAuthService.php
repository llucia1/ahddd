<?php
declare(strict_types=1);

namespace GridCP\Device\Domain\Service;

use GridCP\Device\Domain\VO\Device;
use GridCP\Security\Common\Infrastructure\DB\MySQL\Entity\AuthEntity;

interface ICreateDeviceAuthService
{
    function setAuthDevice( string $uuidDevice, AuthEntity $authEntity): bool;

}