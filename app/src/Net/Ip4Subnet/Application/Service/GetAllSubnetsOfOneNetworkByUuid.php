<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Subnet\Application\Service;

use GridCP\Net\Ip4Subnet\Domain\Repository\IIp4SubnetOwnerRepository;
use GridCP\Net\Ip4Subnet\Domain\VO\UuidClient;
use GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Repository\Ip4SubnetRepository;

use GridCP\Common\Domain\Bus\Query\QueryBus;
use GridCP\Net\Ip4\Application\Cqrs\Queries\GetAllIp4sOfOneNetworkByUuidQueried;
use GridCP\Net\Ip4\Application\Response\GetAllIpsOfOneNetworkExceptionResponse;
use GridCP\Net\Ip4\Domain\Exceptions\ListIp4EmptyException;
use GridCP\Net\Ip4\Domain\Exceptions\NerworkNoExistException;
use GridCP\Net\Ip4Subnet\Application\Response\FloatgroupResponse;
use GridCP\Net\Ip4Subnet\Application\Response\Ip4SubnetsResponses;

use GridCP\Net\Ip4Subnet\Application\Response\Ip4SubnetResponse;
use GridCP\Net\Ip4Subnet\Application\Response\OwnerResponse;
use GridCP\Net\Ip4Subnet\Domain\Exception\GetIpsEmptyOfOneNetworkException;
use GridCP\Net\Ip4Subnet\Domain\Exception\GetIpsOfOneNetworkException;
use GridCP\Net\Ip4Subnet\Domain\Exception\NetworknotExistException;
use GridCP\Net\Ip4Subnet\Domain\Repository\IIp4SubnetRepository;
use GridCP\Net\Ip4Subnet\Domain\VO\UuidNetwork;
use Psr\Log\LoggerInterface;

class GetAllSubnetsOfOneNetworkByUuid
{
    public function __construct(
        private readonly IIp4SubnetRepository $subnetRepository,
        private QueryBus             $queryBus,
        public LoggerInterface       $logger
    ){ }

    public function __invoke(UuidNetwork $uuidNetwork): Ip4SubnetsResponses
    {
        $this->logger->info("Service - Start Get All Subnets Of One Network By Uuid");

        $allIps = $this->queryBus->ask(new GetAllIp4sOfOneNetworkByUuidQueried($uuidNetwork->value()));
        if ($allIps instanceof GetAllIpsOfOneNetworkExceptionResponse) {
            $this->logger->error("Error al obtener las IPs de la red: " . $allIps->get()->getMessage());
            $this->exceptionHandleService($allIps->get());
        }
        
        $allSubnets = $this->getSubnets($allIps->ip4s());
        return $this->subnetsResponses($allSubnets);
    }
    public function subnetsResponses(array $allSubnets): Ip4SubnetsResponses
    {
        $subnetsResponse = array_map(
            fn($subnet) => new Ip4SubnetResponse(
                $subnet->getUuid(),
                $subnet->getIp(),
                $subnet->getMask(),
                new FloatgroupResponse(null,null),
                new OwnerResponse(null,null)
            ),
            $allSubnets
        );
        return new Ip4SubnetsResponses(...$subnetsResponse);
    }
    private function getSubnets(array $ips): array
    {
        $subnets = [];
        foreach ($ips as $ip) {
            $this->logger->info("Check Ip has Subnet: " . $ip->ip());
            $subnet = $this->subnetRepository->findSubnetContainingIp($ip->ip());
            if ($subnet) {
                $subnets[] = $subnet;
            }
        }
        return $subnets;
    }
    private function exceptionHandleService(\Exception $excp): void
    {
        if($excp instanceof NerworkNoExistException){
            throw new NetworknotExistException();

        }elseif($excp instanceof ListIp4EmptyException){
            throw new GetIpsEmptyOfOneNetworkException();

        } else {
            throw new GetIpsOfOneNetworkException();
        }
    }
}