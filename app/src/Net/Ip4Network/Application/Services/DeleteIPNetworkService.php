<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Network\Application\Services;

use GridCP\Net\Ip4Network\Domain\Exception\HasIp4sNetworkException;
use GridCP\Net\Ip4Network\Domain\Exception\ListIp4NetworkEmptyException;
use GridCP\Net\Ip4Network\Domain\Repository\IIp4NetworkRepository;
use GridCP\Net\Ip4Network\Domain\Services\IDeleteIPNetworkService;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkUUID;
use GridCP\Net\Ip4Network\Infrastructure\DB\MySQL\Repository\Ip4NetworkFloatGroupRepository;

class DeleteIPNetworkService implements IDeleteIPNetworkService
{
    public function __construct(
        readonly private IIp4NetworkRepository $ip4NetworkRepository,
        private Ip4NetworkFloatGroupRepository $networkFloatGroupRepository,
    )
    {
    }

    public function __invoke(Ip4NetworkUUID $uuid): ?string
    {
        return $this->delete($uuid);
    }


    public function delete(Ip4NetworkUUID $uuid): ?string
    {
        $network = $this->ip4NetworkRepository->getByUuid($uuid->value());
        if (!$network) {throw new ListIp4NetworkEmptyException();}

        
        $hasIp4s = $this->ip4NetworkRepository->findIpsByNetworkUuid($uuid->value());
        if (!empty($hasIp4s)) {throw new HasIp4sNetworkException();}


        $associated = $this->networkFloatGroupRepository->getByIdNetwork($network->getId());
        if ($associated) {
                $associated->setActive(false);
                $this->networkFloatGroupRepository->save($associated);
        }

        $this->ip4NetworkRepository->delete($network->getUuid());
        return $uuid->value();

    }
}