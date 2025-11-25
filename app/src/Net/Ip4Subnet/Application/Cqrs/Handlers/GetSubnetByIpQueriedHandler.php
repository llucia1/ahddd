<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Subnet\Application\Cqrs\Handlers;


use GridCP\Common\Domain\Bus\Query\QueryHandler;
use GridCP\Net\Ip4Subnet\Application\Cqrs\Queries\GetAllSubnetsQueried;
use GridCP\Net\Ip4Subnet\Application\Cqrs\Queries\GetSubnetByIpQueried;
use GridCP\Net\Ip4Subnet\Application\Help\SubnetsTrait;
use GridCP\Net\Ip4Subnet\Application\Response\Ip4SubnetsResponses;
use GridCP\Net\Ip4Subnet\Application\Response\SubnetArrayResponse;
use GridCP\Net\Ip4Subnet\Application\Response\SubnetEntityResponse;
use GridCP\Net\Ip4Subnet\Application\Service\GetAllSubnets;
use GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Repository\Ip4SubnetRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;



#[AsMessageHandler]
final readonly class GetSubnetByIpQueriedHandler implements QueryHandler
{

    public function __construct(private  readonly  Ip4SubnetRepository $subnetRepository)
    {
    }


    public function __invoke(GetSubnetByIpQueried $ip): SubnetEntityResponse
    {

        try {
            $subnets = $this->subnetRepository->findSubnetContainingIp( $ip->ip() );
            
        }catch(\Exception $ex){
            $subnets = null;
        }
        return new SubnetEntityResponse($subnets);
    }

}