<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Subnet\Common\Service;


use Psr\Log\LoggerInterface;

use Doctrine\ORM\EntityManagerInterface;
use GridCP\Common\Domain\Bus\Query\QueryBus;
use GridCP\Net\Common\Application\Response\GetExceptionResponse;
use GridCP\Net\Ip4FloatGroup\Application\Cqrs\Queries\GetAllNetworkOfoneFloatgroupByUuidQueried;
use GridCP\Net\Ip4Network\Domain\Exception\ListIp4NetworkEmptyException;
use GridCP\Net\Ip4Subnet\Application\Service\GetAllFreeSubnetsOfOneNetworkByUuid;
use GridCP\Net\Ip4Subnet\Domain\Exception\GetIpsEmptyOfOneNetworkException;
use GridCP\Net\Ip4Subnet\Domain\VO\SubnetMask;
use GridCP\Net\Ip4Subnet\Domain\VO\UuidFloatgroup;
use GridCP\Net\Ip4Subnet\Domain\VO\UuidNetwork;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class FreeSubnetsOfAFloatgroupService
{
    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly GetAllFreeSubnetsOfOneNetworkByUuid $freeSubnetsNetworkService, private readonly LoggerInterface $logger, private QueryBus $queryBus,)
    {}
    public function __invoke(UuidFloatgroup $uuidFloatgroup, SubnetMask $mask)
    {
        $this->entityManager->beginTransaction();
        try {
            $this->logger->info('Start All Free Subnet Of A Floatgroup with uuid: ' . $uuidFloatgroup->value() );
            $netwoks = $this->getAllNotworkOfOneFloatgroup($uuidFloatgroup);
            
            $subnets = $this->getSubnets( $netwoks, $mask );
            
            $this->entityManager->commit();
            return $subnets;
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            throw $e;
        }
    }
    private function getSubnets(array $netwoks, SubnetMask $mask):array
    {
        $result = [];
        foreach( $netwoks as $network)
        {
            try {
                $subnet = $this->freeSubnetsNetworkService->__invoke( new UuidNetwork( $network->getUuid() ) , $mask);
                $result = array_merge($result, $subnet);
            } catch ( GetIpsEmptyOfOneNetworkException $e){
                continue;
            }
        }
        return $result;
    }
    private function getAllNotworkOfOneFloatgroup(?UuidFloatgroup $uuidFloatgroup): ?array
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
