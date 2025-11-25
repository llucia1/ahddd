<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Subnet\Domain\Exception;

use RuntimeException;

class ExceptionCreateIP4Subnet extends RuntimeException
{
    public function __construct(string $rangeName)
    {
        parent::__construct(sprintf('Error creating IP subnet <%s> in system', $rangeName));
    }
}