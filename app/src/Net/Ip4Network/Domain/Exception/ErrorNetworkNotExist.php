<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Network\Domain\Exception;



final class ErrorNetworkNotExist extends \Exception
{
    public function __construct(?string $id)
    {
        parent::__construct(sprintf("Error Network Not Exist -> ".$id));
    }
}