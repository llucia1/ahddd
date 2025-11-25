<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Network\Domain\Exception;

use Error;

final class IP4NetworkDuplicated extends Error
{
    public function __construct(String $ip4Network)
    {
        parent::__construct('Ip4 Network Duplicated ->'.$ip4Network);
    }
}