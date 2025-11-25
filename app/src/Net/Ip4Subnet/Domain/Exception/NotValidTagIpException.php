<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Subnet\Domain\Exception;





class NotValidTagIpException extends \Exception
{
    public function __construct(?string $msn = null, ?string $tag = null)
    {
        parent::__construct(sprintf('Invalid IP %s With Tag %s ', ($msn ? '-> '.$msn : ''), $tag ? strtoupper($tag) : 'NULL'), 404);
    }
}