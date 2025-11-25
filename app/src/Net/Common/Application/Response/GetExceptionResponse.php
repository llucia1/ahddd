<?php
declare(strict_types=1);

namespace GridCP\Net\Common\Application\Response;



use GridCP\Common\Domain\Bus\Query\Response;

final class GetExceptionResponse implements Response
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

    public function floatgroup(): \Exception
    {
        return $this->exception;
    }

}