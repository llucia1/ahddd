<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Subnet\Application\Cqrs\Handlers;


use GridCP\Common\Domain\Bus\Query\QueryHandler;
use GridCP\Net\Ip4Subnet\Application\Cqrs\Queries\DeleteSubnetByIpQueried;
use GridCP\Net\Ip4Subnet\Application\Help\SubnetsTrait;
use GridCP\Net\Ip4Subnet\Application\Response\Ip4SubnetAvaibleResponse;
use GridCP\Net\Ip4Subnet\Application\Response\Ip4SubnetsResponses;
use GridCP\Net\Ip4Subnet\Application\Response\SubnetArrayResponse;
use GridCP\Net\Ip4Subnet\Application\Response\SubnetEntityResponse;
use GridCP\Net\Ip4Subnet\Application\Service\DeleteSubnetService;
use GridCP\Net\Ip4Subnet\Application\Service\GetAllSubnets;
use GridCP\Net\Ip4Subnet\Domain\VO\SubnetUuid;
use GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Repository\Ip4SubnetRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;



#[AsMessageHandler]
final readonly class DeleteSubnetByIpQueriedHandler implements QueryHandler
{
    use SubnetsTrait;

    public function __construct(private  readonly  Ip4SubnetRepository $subnetRepository, 
                                private  readonly  DeleteSubnetService $deleteSubnet)
    {
    }


    public function __invoke(DeleteSubnetByIpQueried $query): Ip4SubnetAvaibleResponse
    {
        try {
            $subnet = $this->subnetRepository->findSubnetContainingIp( $query->ip() );
            $this->deleteSubnet->__invoke( new SubnetUuid($subnet->getUuid()) );
            $ip = $subnet->getIp();
            $mask = $subnet->getMask();
            $uuid = $subnet->getUuid();
        }catch(\Exception $ex){
            $ip = null;
            $mask = null;
            $uuid = null;
        }
        return new Ip4SubnetAvaibleResponse($ip, $mask, $uuid);
    }

}