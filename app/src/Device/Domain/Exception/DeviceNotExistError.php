<?php
declare(strict_types=1);
namespace GridCP\Device\Domain\Exception;




final class DeviceNotExistError extends \Error
{
    public function __construct()
    {
        parent::__construct(sprintf("Device not found Error"), 404);
    }
}