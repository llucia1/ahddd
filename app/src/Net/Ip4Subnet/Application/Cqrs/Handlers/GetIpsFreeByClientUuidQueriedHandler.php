<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Subnet\Application\Cqrs\Handlers;


use GridCP\Common\Domain\Bus\Query\QueryHandler;
use GridCP\Net\Ip4Subnet\Application\Cqrs\Queries\GetIpsFreeByClientUuidQueried;
use GridCP\Net\Ip4Subnet\Application\Response\SubnetArrayResponse;
use GridCP\Net\Ip4Subnet\Application\Service\GetAllSubnetsByClientByUuid;
use GridCP\Net\Ip4Subnet\Domain\VO\UuidClient;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;



#[AsMessageHandler]
final readonly class GetIpsFreeByClientUuidQueriedHandler implements QueryHandler
{


    public function __construct(private  readonly  GetAllSubnetsByClientByUuid $subnetService)
    {
    }


    public function __invoke(GetIpsFreeByClientUuidQueried $client): SubnetArrayResponse
    {

        try {
            $result = $this->subnetService->__invoke( $client->uuid() ? new UuidClient($client->uuid()) : null );
             
        }catch(\Exception $ex){
            $result = null;
        }

        return new SubnetArrayResponse($result);
    }

}