<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4FloatGroup\Application\Responses;
use GridCP\Common\Domain\Bus\Query\Response;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4FloatGroupEntity;

final readonly class FloatGroupEntityResponse  implements Response
{

 public function __construct(private ?Ip4FloatGroupEntity $fgEntity){}

    public function get():?Ip4FloatGroupEntity
    {
        return $this->fgEntity;
    }
}