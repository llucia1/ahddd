<?php

namespace GridCP\Device\Domain\Exception;



use Exception;

final class CreateDeviceEventError extends Exception
{
    public function __construct(Exception $e)
    {
        parent::__construct(sprintf('Error in create Device', $e->getMessage()));
    }
}