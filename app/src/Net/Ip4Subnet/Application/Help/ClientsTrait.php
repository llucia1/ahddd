<?php
namespace GridCP\Net\Ip4Subnet\Application\Help;

use GridCP\Client\Application\Cqrs\Queries\GetClientEntityByUuidQuerie;
use GridCP\Client\Application\Response\ClientEntityResponse;
use GridCP\Common\Domain\Bus\Query\QueryBus;
use GridCP\Net\Ip4Subnet\Application\Response\FloatgroupResponse;
use GridCP\Net\Ip4Subnet\Application\Response\Ip4SubnetResponse;
use GridCP\Net\Ip4Subnet\Application\Response\Ip4SubnetsResponses;
use GridCP\Net\Ip4Subnet\Application\Response\OwnerResponse;
use GridCP\Net\Ip4Subnet\Domain\Exception\ClientNoFound;
use GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Entity\Ip4SubnetEntity;
use GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Entity\Ip4SubnetOwnerEntity;

use function Lambdish\Phunctional\map;
trait ClientsTrait
{


    private function existClient(string $clientUuid, QueryBus $queryBus): ClientEntityResponse
    {
        try {
            return $queryBus->ask(new GetClientEntityByUuidQuerie($clientUuid));
        }catch(\Exception $ex){
            throw new ClientNoFound($clientUuid);
        }
    }

    private function hasPermissionsClient(string $clientUuid, QueryBus $queryBus): ClientEntityResponse
    {
        try {
            return $queryBus->ask(new GetClientEntityByUuidQuerie($clientUuid));
        }catch(\Exception $ex){
            throw new ClientNoFound($clientUuid);
        }
    }


}