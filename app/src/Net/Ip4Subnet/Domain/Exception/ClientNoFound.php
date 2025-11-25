<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Subnet\Domain\Exception;


class ClientNoFound extends \Exception
{
    public function __construct( ?string $clientUuid )
    {
        parent::__construct('Not Found Client with uuid = ' . $clientUuid);
    }
}