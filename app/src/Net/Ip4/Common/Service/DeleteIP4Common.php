<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4\Common\Service;

use GridCP\Common\Domain\Bus\Query\QueryBus;
use GridCP\Net\Ip4\Domain\VO\Ip4Ip;

use GridCP\Net\Ip4\Domain\VO\Ip4Ips;
use GridCP\Net\Ip4\Infrastructure\DB\MySQL\Repository\Ip4Repository;
use Psr\Log\LoggerInterface;

use GridCP\Net\Common\Application\Helpers\CalcIps;
use GridCP\Net\Ip4\Domain\Exceptions\Ip4GenuineNotValidDelete;
use GridCP\Net\Ip4\Domain\Exceptions\Ip4InSubnetNotValidDelete;
use GridCP\Net\Ip4\Domain\Service\IDeleteIp4Service;
use GridCP\Net\Ip4Subnet\Domain\Repository\IIp4SubnetRepository;
use GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Repository\Ip4SubnetRepository;
use GridCP\Proxmox\Vm\Application\Cqrs\Queries\GetVmIp4ByIp4Queried;
use IPCalc\IP;

class DeleteIP4Common implements IDeleteIp4Service
{
    use CalcIps;
    public function __construct(
                                    private readonly Ip4Repository $ip4Repository,
                                    private  readonly  LoggerInterface $logger,
                                    private readonly IIp4SubnetRepository $ip4SubnetRepository,
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
        $this->logger->info("Start Service Domain: delete All IP4s: ");
        $notFound = [];
        foreach ($ip4s->get() as $ip4) {
            $ipNotFound = $this->deleteIP4($ip4);
            if ($ipNotFound) { $notFound = array_merge($notFound, $ipNotFound); }
        }
        return $notFound;
    }

    public function deleteIP4(Ip4Ip $ip4): ?array
    {
        $this->logger->info("Start Service Domain: delete One IP4: " . $ip4->value());
        $ip4s = $this->getIp4s( new IP($ip4->value()));
        return $this->deleteAllIp4s( $ip4s );
    }


    public function deleteAllIp4s(array $ip4s ): ?array
    {
        $notFound = [];
        foreach ($ip4s as $ip4) {
            $this->checkValidIp4ToDelete( $ip4);
            if (!$this->ip4Repository->deleteByIp($ip4))
                { $notFound[] = $ip4;}
        }
        return empty( $notFound ) ? null : $notFound;
    }


    public function checkValidIp4ToDelete(string $ip4): void
    {
        $subnet = $this->ip4SubnetRepository->findSubnetContainingIp( $ip4 );
        if ($subnet) {
            throw new Ip4InSubnetNotValidDelete($ip4);
        }
        $vmIp4 = $this->queryBus->ask(new GetVmIp4ByIp4Queried($ip4));
        if ($vmIp4->get()) {
            throw new Ip4GenuineNotValidDelete($ip4);
        }
        
    }
}
