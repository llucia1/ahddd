<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Subnet\Domain\Exception;





class IpsDuplicatedException extends \Exception
{
    public function __construct( array $ipsDuplicated )
    {
        parent::__construct('Ips Duplicated: ' .  implode(', ', $ipsDuplicated )  );
    }
}