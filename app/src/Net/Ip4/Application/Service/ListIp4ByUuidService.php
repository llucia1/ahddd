<?php

namespace GridCP\Net\Ip4\Application\Service;

use GridCP\Net\Common\Application\Helpers\Ip4Trait;
use GridCP\Net\Ip4\Application\Response\Ip4Response;
use GridCP\Net\Ip4\Application\Response\Ip4WithNetworkResponse;
use GridCP\Net\Ip4\Domain\Exceptions\Ip4NoFoundException;

use GridCP\Net\Ip4\Domain\Repository\IIp4Repository;
use GridCP\Net\Ip4\Domain\Service\IListIp4ByUuidService;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4Entity;
use GridCP\Net\Ip4\Application\Response\networksResponse;
use GridCP\Net\Ip4\Domain\VO\Ip4Uuid;

class ListIp4ByUuidService implements IListIp4ByUuidService
{
    use Ip4Trait;
    public function __construct(
        readonly private IIp4Repository $ip4Repository,
    )
    {
    }

    public function __invoke(Ip4Uuid $uuid): Ip4Response
    {
        return $this->getByUuid($uuid);

    }

    public function getByUuid(Ip4Uuid $uuid): Ip4Response
    {
        $ip = $this->ip4Repository->findByUuidWithActiveTag($uuid->value());
        return $ip
            ? $this->toResponse($ip)
            : throw new Ip4NoFoundException();
    }

    public function toResponse(Ip4Entity $ip4Entity): Ip4Response
    {
        return new Ip4Response(
            $ip4Entity->uuid,
            $ip4Entity->ip,
            ($ip4Entity->getNetwork()) ? new Ip4WithNetworkResponse(
                                                                                $ip4Entity->getNetwork()->getUuid(),
                                                                                $ip4Entity->getNetwork()->getName()
                                                                            ) : null,
            $ip4Entity->active,
            $ip4Entity->priority,
            self::getOnlyActiveTag($ip4Entity)
        );
    }
}