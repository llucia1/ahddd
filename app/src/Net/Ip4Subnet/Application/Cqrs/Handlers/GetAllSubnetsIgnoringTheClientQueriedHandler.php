<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Subnet\Application\Cqrs\Handlers;


use GridCP\Common\Domain\Bus\Query\QueryHandler;
use GridCP\Net\Ip4Subnet\Application\Cqrs\Queries\GetAllSubnetsIgnoringTheClientQueried;
use GridCP\Net\Ip4Subnet\Application\Help\SubnetsTrait;
use GridCP\Net\Ip4Subnet\Application\Response\Ip4SubnetsResponses;
use GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Entity\Ip4SubnetEntity;

use function Lambdish\Phunctional\map;

use GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Repository\Ip4SubnetRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;



#[AsMessageHandler]
final readonly class GetAllSubnetsIgnoringTheClientQueriedHandler implements QueryHandler
{
    use SubnetsTrait;

    public function __construct(private  readonly  Ip4SubnetRepository $subnetRepository)
    {
    }


    public function __invoke(GetAllSubnetsIgnoringTheClientQueried $query): Ip4SubnetsResponses
    {
        try {
            $subnets = $this->subnetRepository->getAllWithRelation();
            return $this->subnetsClientResponses($subnets);
        }catch(\Exception $ex){
            return $this->subnetsClientResponses([]);
        }

    }

}