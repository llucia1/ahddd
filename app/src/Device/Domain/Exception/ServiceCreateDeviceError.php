<?php
declare(strict_types=1);
namespace GridCP\Device\Domain\Exception;
use \Error;
class ServiceCreateDeviceError extends Error
{
    public  function __construct()
    {
        parent::__construct(sprintf("Error in service create Device"), 422);
    }
}
