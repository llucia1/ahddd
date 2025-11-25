<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Subnet\Application\Service;


use Psr\Log\LoggerInterface;

use Doctrine\ORM\EntityManagerInterface;
use GridCP\Common\Domain\Bus\Query\QueryBus;
use GridCP\Common\Domain\Const\Ip4\Tags;
use GridCP\Net\Common\Application\Helpers\CalcIps;
use GridCP\Net\Common\Application\Response\GetExceptionResponse;
use GridCP\Net\Ip4\Application\Cqrs\Queries\GetAllIp4sOfOneNetworkByUuidQueried;
use GridCP\Net\Ip4\Application\Response\GetAllIpsOfOneNetworkExceptionResponse;
use GridCP\Net\Ip4\Application\Response\Ip4Response;
use GridCP\Net\Ip4FloatGroup\Application\Cqrs\Queries\GetAllNetworkOfoneFloatgroupByUuidQueried;
use GridCP\Net\Ip4Network\Domain\Exception\ListIp4NetworkEmptyException;
use GridCP\Net\Ip4Subnet\Application\Response\SubnetFreeResponse;
use GridCP\Net\Ip4Subnet\Domain\VO\SubnetMask;
use GridCP\Net\Ip4Subnet\Domain\VO\UuidFloatgroup;
use GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Repository\Ip4SubnetRepository;
use IPCalc\IP;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class FreeSubnetsOfAFloatgroupService
{
    use CalcIps;
    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly Ip4SubnetRepository $ip4SubnetRepository, private readonly LoggerInterface $logger, private QueryBus $queryBus,)
    {}
    public function __invoke(UuidFloatgroup $uuidFloatgroup, SubnetMask $mask): SubnetFreeResponse
    {
        $this->entityManager->beginTransaction();
        try {
            $this->logger->info('Start All Free Subnet Of A Floatgroup with uuid: ' . $uuidFloatgroup->value() );
            $netwoks = $this->getAllNetworkOfOneFloatgroup($uuidFloatgroup);
            

            $allIpsOfNetworks = $this->getAllIpsOfNetwokrs($netwoks);
            $validIps = array_filter($allIpsOfNetworks, function (Ip4Response $ip) {
                $tag = $ip->tag();
                return is_null($tag) || Tags::isValidTag($tag->tag());
            });

            $allIpsOfSubnets = $this->getAllIpsOfSubnets( $uuidFloatgroup );
            $allIpsUniques = $this->getIpsUnique( $allIpsOfSubnets, $this->getOnlyIpOfNetwork($validIps));
            $subnets = $this->getSubnetsFromIps( $allIpsUniques , $mask->getValue() );
            
            $this->entityManager->commit();
            return new SubnetFreeResponse($subnets, $mask->getValue());
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            throw $e;
        }
    }


    private function getIpsNetworks( $network): array
    {
        $allIps = $this->queryBus->ask(new GetAllIp4sOfOneNetworkByUuidQueried($network->getUuid() ));
        if ($allIps instanceof GetAllIpsOfOneNetworkExceptionResponse) {
            $this->logger->error("Error al obtener las IPs de la red: " . $allIps->get()->getMessage());
            $this->exceptionHandleService($allIps->get());
        }
        return $allIps->ip4s();
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

    private function getAllIpsOfNetwokrs(array $netwoks): array
    {
        $result = [];
        foreach( $netwoks as $network)
        {
            $result = array_merge($result, $this->getIpsNetworks($network));
        }
        return $result;
    }

    private function getIpsUnique( $allIpsOfSubnets, $allIpsOfNetworks): array
    {
        $result = [];
        foreach( $allIpsOfNetworks as $ip)
        {
            if (!in_array($ip, $allIpsOfSubnets)) {
                $result[] = $ip;
            }
        }
        return $result;
    }

    private function getAllIpsOfSubnets(UuidFloatgroup $uuidFloatgroup): array
    {
        $subnets = $this->ip4SubnetRepository->getAllSubnetsByFloatgroupUuid($uuidFloatgroup->value());
        $result = [];
        foreach( $subnets as $subnet)
        {
            $result = array_merge($result, $this->getIp4s(new IP($subnet->getIp(), $subnet->getMask() ) ));
        }
        return $result;
    }



    private function getAllNetworkOfOneFloatgroup(?UuidFloatgroup $uuidFloatgroup): ?array
    {
        if (!$uuidFloatgroup || !$uuidFloatgroup->value()) {
            return null;
        }
        
        $netwoks = $this->queryBus->ask(new GetAllNetworkOfoneFloatgroupByUuidQueried($uuidFloatgroup->value()));
        if ($netwoks instanceof GetExceptionResponse) {
            $this->logger->error("Error Get All Network: " . $netwoks->get()->getMessage());
            $this->exceptionHandleService($netwoks->get());
        }
        return $netwoks->get();
    }
    private function exceptionHandleService(\Exception $excp): void
    {
        if($excp instanceof ListIp4NetworkEmptyException){
            throw new ListIp4NetworkEmptyException();

        }elseif($excp instanceof HttpException){
            throw $excp;

        }
    }
}
