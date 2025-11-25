<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Network\Application\Services;

use Exception;
use GridCP\Net\Ip4Network\Application\Responses\Ip4NetworkResponse;
use GridCP\Net\Ip4Network\Domain\Exception\ListIp4NetworkEmptyException;
use GridCP\Net\Ip4Network\Domain\Repository\IIp4NetworkRepository;
use GridCP\Net\Ip4Network\Domain\Services\IListIP4NetworkByUUIDService;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4NetworkEntity;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkUUID;

class ListIP4NetworkByUUIDService implements IListIP4NetworkByUUIDService
{

    public function __construct(
        readonly private IIp4NetworkRepository $ip4NetworkRepository,
    )
    {
    }

    public function __invoke(Ip4NetworkUUID $uuid): Ip4NetworkResponse
    {

     return $this->getAll($uuid);
    }

    public function getAll(Ip4NetworkUUID $uuid): Ip4NetworkResponse
    {
        $existingNetwork = $this->ip4NetworkRepository->getByUuid($uuid->value());
        return  $existingNetwork
            ? $this->toResponse($existingNetwork)
            : throw new ListIp4NetworkEmptyException();
    }

    public function toResponse(Ip4NetworkEntity $ip4NetworkEntity):Ip4NetworkResponse
    {
       return  new Ip4NetworkResponse(
           $ip4NetworkEntity->getUuid(),
           $ip4NetworkEntity->getName(),
           $ip4NetworkEntity->getNameServer1(),
           $ip4NetworkEntity->getNameServer2(),
           $ip4NetworkEntity->getNameServer3(),
           $ip4NetworkEntity->getNameServer4(),
           $ip4NetworkEntity->getPriority(),
           $ip4NetworkEntity->getNetmask(),
           $ip4NetworkEntity->getGateway(),
           $ip4NetworkEntity->getBroadcast(),
           $ip4NetworkEntity->getFloatGroup(),
           $ip4NetworkEntity->getId()
       );
    }


}