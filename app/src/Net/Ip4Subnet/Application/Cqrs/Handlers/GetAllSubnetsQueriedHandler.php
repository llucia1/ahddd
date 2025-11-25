<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Subnet\Application\Cqrs\Handlers;


use GridCP\Common\Domain\Bus\Query\QueryHandler;
use GridCP\Net\Ip4Subnet\Application\Cqrs\Queries\GetAllSubnetsQueried;
use GridCP\Net\Ip4Subnet\Application\Help\SubnetsTrait;
use GridCP\Net\Ip4Subnet\Application\Response\Ip4SubnetsResponses;
use GridCP\Net\Ip4Subnet\Application\Response\SubnetArrayResponse;
use GridCP\Net\Ip4Subnet\Application\Service\GetAllSubnets;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;



#[AsMessageHandler]
final readonly class GetAllSubnetsQueriedHandler implements QueryHandler
{
    use SubnetsTrait;

    public function __construct(private  readonly  GetAllSubnets $subnetService)
    {
    }


    public function __invoke(GetAllSubnetsQueried $query): Ip4SubnetsResponses
    {

        try {
            $subnets = $this->subnetService->__invoke( $query->clientUuid() );
            return $this->subnetsClientResponses($subnets);
        }catch(\Exception $ex){
            return $this->subnetsClientResponses([]);
        }
    }

}