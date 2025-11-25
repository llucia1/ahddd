<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Subnet\Application\Cqrs\Handlers;


use GridCP\Common\Domain\Bus\Query\QueryHandler;
use GridCP\Net\Ip4Subnet\Application\Cqrs\Queries\PostOwnerSubnetQueried;
use GridCP\Net\Ip4Subnet\Application\Response\CreatedOwnerSubnetResponse;
use GridCP\Net\Ip4Subnet\Application\Service\AddPropertySubnet;
use GridCP\Net\Ip4Subnet\Domain\VO\SubnetUuid;
use GridCP\Net\Ip4Subnet\Domain\VO\UuidClient;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;



#[AsMessageHandler]
final readonly class PostOwnerSubnetQueriedHandler implements QueryHandler
{


    public function __construct(private readonly AddPropertySubnet $propertySubnetService)
    {
    }


    public function __invoke(PostOwnerSubnetQueried $subnet): CreatedOwnerSubnetResponse
    {

        try {


            $subnetUUid = new SubnetUuid($subnet->subnetuuid());// NOSONAR
            $subnetUuidClient = $subnet->clientUuid() ? new UuidClient($subnet->clientUuid()) : null;
            $result = $this->propertySubnetService->__invoke($subnetUUid, $subnetUuidClient);
            
            
        } catch ( \Exception $e){
            $result = $e;
        }

        return new CreatedOwnerSubnetResponse($result);
    }

}