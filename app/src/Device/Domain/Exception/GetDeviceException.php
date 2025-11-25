<?php
declare(strict_types=1);

namespace GridCP\Device\Domain\Exception;

use Error;

class GetDeviceException extends Error
{
    public function __construct(Error $e)
    {
        parent::__construct(sprintf($e->getMessage(), 'Error obtain list Devices'));
    }
}