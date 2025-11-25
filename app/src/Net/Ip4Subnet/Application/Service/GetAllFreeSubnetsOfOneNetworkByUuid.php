<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Subnet\Application\Service;

use GridCP\Common\Domain\Bus\Query\QueryBus;
use GridCP\Common\Domain\Const\Ip4\Tags;
use GridCP\Net\Common\Application\Helpers\CalcIps;
use GridCP\Net\Ip4\Application\Cqrs\Queries\GetAllIp4sOfOneNetworkByUuidQueried;
use GridCP\Net\Ip4\Application\Response\GetAllIpsOfOneNetworkExceptionResponse;
use GridCP\Net\Ip4\Application\Response\Ip4Response;
use GridCP\Net\Ip4\Domain\Exceptions\ListIp4EmptyException;
use GridCP\Net\Ip4\Domain\Exceptions\NerworkNoExistException;
use GridCP\Net\Ip4Subnet\Application\Response\Ip4SubnetAvaibleResponse;
use GridCP\Net\Ip4Subnet\Application\Response\Ip4SubnetsResponses;

use GridCP\Net\Ip4Subnet\Application\Response\Ip4SubnetResponse;

use GridCP\Net\Ip4Subnet\Domain\Exception\GetIpsEmptyOfOneNetworkException;
use GridCP\Net\Ip4Subnet\Domain\Exception\GetIpsOfOneNetworkException;
use GridCP\Net\Ip4Subnet\Domain\Exception\NetworknotExistException;
use GridCP\Net\Ip4Subnet\Domain\Exception\SubnetsNoFound;
use GridCP\Net\Ip4Subnet\Domain\Repository\IIp4SubnetRepository;
use GridCP\Net\Ip4Subnet\Domain\VO\SubnetMask;
use GridCP\Net\Ip4Subnet\Domain\VO\UuidNetwork;
use Psr\Log\LoggerInterface;

class GetAllFreeSubnetsOfOneNetworkByUuid
{

    use CalcIps;
    public function __construct(
        private readonly IIp4SubnetRepository $subnetRepository,
        private QueryBus             $queryBus,
        public LoggerInterface       $logger
    ){ }

    public function __invoke(UuidNetwork $uuidNetwork, SubnetMask $mask): array
    {
        $this->logger->info("Service - Start Get All Subnets Of One Network By Uuid");

        $allIps = $this->queryBus->ask(new GetAllIp4sOfOneNetworkByUuidQueried($uuidNetwork->value()));
        if ($allIps instanceof GetAllIpsOfOneNetworkExceptionResponse) {
            $this->logger->error("Error al obtener las IPs de la red: " . $allIps->get()->getMessage());
            $this->exceptionHandleService($allIps->get());
        }
        
        $validIps = array_filter($allIps->ip4s(), function (Ip4Response $ip) {
            $tag = $ip->tag();
            return is_null($tag) || Tags::isValidTag($tag->tag());
        });

        $allSubnetsAvaible = $this->findAvailableSubnets($this->getOnlyIpOfNetwork($validIps), $mask->getValue());

        if (empty($allSubnetsAvaible)) {
            $this->logger->error("Not Found Subnets with mask: " . $mask->getValue());
            throw new SubnetsNoFound();
        }
        return $this->getSubnetsResponse($allSubnetsAvaible, $mask->getValue());
    }
    
    private function getOnlyIpOfNetwork(array $ips): array
    {
        $results = [];
        foreach ($ips as $ip) {
            if ($ip) {
                $results[] = $ip->ip();
            }
        }
        return $results;
    }
    private function getSubnetsResponse(array $ips, $mask): array
    {
        $subnets = [];
        foreach ($ips as $ip) {
            $this->logger->info("Check Ip has Subnet: " . $ip);
            $subnet = $this->subnetRepository->findSubnetContainingIp($ip);
            if (!$subnet) {
                $subnets[] = new Ip4SubnetAvaibleResponse(
                    $ip,
                    $mask
                );
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