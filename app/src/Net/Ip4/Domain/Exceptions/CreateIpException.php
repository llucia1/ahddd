<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4\Domain\Exceptions;

use Error;

final class CreateIpException extends Error
{
    public function __construct(Error $e)
    {
        parent::__construct(sprintf('Error creating IP4 <%s> in system', $e->getMessage()));
    }
}