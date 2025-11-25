<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4\Application\Service;

use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4Entity;
use GridCP\Net\Common\Application\Helpers\Ip4Trait;
use GridCP\Net\Ip4\Application\Response\Ip4Response;
use GridCP\Net\Ip4\Domain\Exceptions\ListIp4EmptyException;
use GridCP\Net\Ip4\Domain\Repository\IIp4Repository;
use GridCP\Net\Ip4\Domain\Service\IListIp4Service;
use GridCP\Net\Ip4\Application\Response\Ip4sResponse;
use GridCP\Net\Ip4\Application\Response\Ip4WithNetworkResponse;


use function Lambdish\Phunctional\map;
use GridCP\Net\Ip4\Domain\Model\Ip4;
use GridCP\Net\Ip4Tag\Application\Response\Ip4TagResponse;

class ListIp4Service implements IListIp4Service
{
    use Ip4Trait;
    public function __construct(private readonly IIp4Repository $ip4Repository) 
    {
    }

    public function __invoke(): Ip4sResponse
    {
        return $this->getAll();
    }

    public function getAll(): Ip4sResponse
    {
        $ip4s = $this->ip4Repository->getAllWithActiveTagAndNetwork();
        return empty($ip4s)
            ? throw new ListIp4EmptyException()
            : new Ip4sResponse(...map($this->toResponse(), $ip4s));
    }

    public function toResponse(): callable
    {
        return static fn (Ip4Entity $ip4): Ip4Response => new Ip4Response(
            $ip4->uuid,
            $ip4->ip,
            $ip4->getNetwork() && $ip4->getNetwork()->active
                ? new Ip4WithNetworkResponse(
                    $ip4->getNetwork()->uuid,
                    $ip4->getNetwork()->name
                )
                : null,
            null,
            $ip4->priority,
            self::getOnlyActiveTag($ip4)
        );
    }
}