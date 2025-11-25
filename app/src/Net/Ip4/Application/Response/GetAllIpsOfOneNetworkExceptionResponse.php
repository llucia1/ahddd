<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4\Application\Response;

use GridCP\Common\Domain\Bus\Query\Response;

final class GetAllIpsOfOneNetworkExceptionResponse implements Response
{
    private readonly \Exception $exception;

    public function __construct( \Exception $exception)
    {
        $this->exception = $exception;
    }

    public function get(): \Exception
    {
        return $this->exception;
    }

}