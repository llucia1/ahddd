<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4\Application\Service;

use GridCP\Common\Domain\Bus\Query\QueryBus;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4Entity;
use GridCP\Net\Ip4\Common\Service\DeleteIP4Common;
use GridCP\Net\Ip4\Domain\Exceptions\Ip4AreAsignnedException;
use GridCP\Net\Ip4\Domain\Exceptions\Ip4sInSubnetsException;
use GridCP\Net\Ip4\Domain\Exceptions\ListIp4EmptyException;

use GridCP\Net\Ip4\Domain\Exceptions\NerworkNoExistException;
use GridCP\Net\Ip4\Domain\Service\IDeleteAllIp4OfNetwork;


use GridCP\Net\Ip4\Domain\VO\Ip4UuidNetwork;

use GridCP\Net\Ip4\Domain\VO\Ip4Ips;
use GridCP\Net\Ip4\Infrastructure\DB\MySQL\Repository\Ip4Repository;
use GridCP\Net\Ip4Network\Application\Cqrs\Queries\GetNetworkByUuidQueried;
use Psr\Log\LoggerInterface;
use GridCP\Proxmox\Vm\Application\Helpers\IpsTrait;

class DeleteAllIp4OfNetwork
{
    use IpsTrait;
    public function __construct(
                                    private readonly Ip4Repository $ip4Repository,
                                    private DeleteIP4Common $deleteIpsService,
                                    public LoggerInterface       $logger,
                                    private QueryBus             $queryBus,
                                )
    {
    }

    public function __invoke(Ip4UuidNetwork $netWorkUuid): void
    {
        $this->deleteAllIp4OfNetwork($netWorkUuid);
    }

    public function deleteAllIp4OfNetwork(Ip4UuidNetwork $networkUuid):void
    {
        $this->logger->info("Service - Start Get all IP4 By Netwok Uuid");
            
        try {
            $responseQuery = $this->queryBus->ask(new GetNetworkByUuidQueried($networkUuid->value()));
        }catch(\Exception $ex){
            throw new NerworkNoExistException();
        }

        $this->logger->info("Service - Get one Netwok By Uuid: ".$responseQuery->get()->id()." with uuid ".$responseQuery->get()->uuid());
        $allIp4s = $this->ip4Repository->findAllByNetworkid($responseQuery->get()->id());

        empty($allIp4s)
            ? throw new ListIp4EmptyException()
            : $this->deleteAll($allIp4s);
    }

    private function deleteAll(array $allIp4s): void
    {

        $ipsAssignedToVmIp4 = $this->checkAnyIpAssingned( $allIp4s, $this->ip4Repository);
        if (!empty($ipsAssignedToVmIp4)) {
            throw new Ip4AreAsignnedException($ipsAssignedToVmIp4);
        }
        $ips = array_map(fn(Ip4Entity $ip4) => $ip4->getIp(), $allIp4s);
        $ipsInSubnets = $this->checkAnyIpInSubnet( $ips, $this->queryBus);
        if (!empty($ipsInSubnets)) {
            throw new Ip4sInSubnetsException($ipsInSubnets);
        }
        $this->deleteIpsService->deleteIP4s(new Ip4Ips(array_map(fn($ipEntity) => $ipEntity->getIp(), $allIp4s)));
    }
}