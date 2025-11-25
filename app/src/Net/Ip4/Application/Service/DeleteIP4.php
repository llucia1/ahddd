<?php

declare(strict_types=1);

namespace GridCP\Net\Ip4\Application\Service;

use GridCP\Common\Domain\Bus\Query\QueryBus;
use GridCP\Net\Ip4\Common\Service\DeleteIP4Common;
use GridCP\Net\Ip4\Domain\Exceptions\Ip4AreAsignnedException;
use GridCP\Net\Ip4\Domain\Exceptions\Ip4sInSubnetsException;
use GridCP\Net\Ip4\Domain\VO\Ip4Ip;
use GridCP\Net\Ip4\Domain\VO\Ip4Ips;
use GridCP\Net\Ip4\Infrastructure\DB\MySQL\Repository\Ip4Repository;
use GridCP\Proxmox\Vm\Application\Helpers\IpsTrait;
use Psr\Log\LoggerInterface;


class DeleteIP4
{
    use IpsTrait;
    public function __construct(
                                    private readonly Ip4Repository $ip4Repository,
                                    private DeleteIP4Common $deleteIpsService,
                                    private  readonly  LoggerInterface $logger,
                                    private QueryBus             $queryBus
                                )
    {
    }

    public function __invoke(Ip4Ips $ip4s): ?array
    {
        return $this->deleteIP4s($ip4s);
    }

    public function deleteIP4s(Ip4Ips $ip4s): ?array
    {
            $ips4 = $this->extractIpStrings($ip4s->get());
            $ipsAssignedToVmIp4 = $this->checkAnyIpAssingned( $ips4, $this->ip4Repository);
            if (!empty($ipsAssignedToVmIp4)) {
                throw new Ip4AreAsignnedException($ipsAssignedToVmIp4);
            }
            $ipsInSubnets = $this->checkAnyIpInSubnet( $ips4, $this->queryBus);
            if (!empty($ipsInSubnets)) {
                throw new Ip4sInSubnetsException($ipsInSubnets);
            }

            return $this->deleteIpsService->deleteIP4s($ip4s);
    }

    private function extractIpStrings(array $ipObjects): array
    {
        return array_map(fn(Ip4Ip $ip) => $ip->value(), $ipObjects);
    }
}
