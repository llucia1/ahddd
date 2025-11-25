<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Network\Domain\Exception;



final class ErrorNetworkNotActive extends \Exception
{
    public function __construct(?string $id)
    {
        parent::__construct(sprintf("Error Network Not Active -> ".$id));
    }
}