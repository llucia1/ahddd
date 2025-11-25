<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4\Application\Response;


use GridCP\Common\Domain\Bus\Query\Response;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4Entity;

final class Ip4EntityResponse implements Response
{
    public function __construct(
                                    private ?Ip4Entity $ip
                                ){
                                }

    public function get(): ?Ip4Entity
    {
        return $this->ip;
    }
}