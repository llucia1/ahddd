<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4\Domain\Exceptions;

use Error;


class GetIP4Error extends Error
{
    public function __construct(Error $e)
    {
        parent::__construct(sprintf('Error obtain list ip4', $e->getMessage()));
    }

}