<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Subnet\Application\Cqrs\Handlers;


use GridCP\Common\Domain\Bus\Query\QueryHandler;
use GridCP\Net\Ip4Subnet\Application\Cqrs\Queries\PostSubnetQueried;
use GridCP\Net\Ip4Subnet\Application\Response\CreatedSubnetResponse;
use GridCP\Net\Ip4Subnet\Application\Service\CreateIp4Subnet;
use GridCP\Net\Ip4Subnet\Domain\VO\Ip4SubnetVo;
use GridCP\Net\Ip4Subnet\Domain\VO\SubnetIP;
use GridCP\Net\Ip4Subnet\Domain\VO\SubnetMask;
use GridCP\Net\Ip4Subnet\Domain\VO\SubnetUuid;
use GridCP\Net\Ip4Subnet\Domain\VO\UuidFloatgroup;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;



#[AsMessageHandler]
final readonly class PostSubnetQueriedHandler implements QueryHandler
{


    public function __construct(private  readonly  CreateIp4Subnet $subnetService)
    {
    }


    public function __invoke(PostSubnetQueried $subnet): CreatedSubnetResponse
    {

        try {
            $subnetUUid = new SubnetUuid($subnet->uuid() ? $subnet->uuid() : SubnetUuid::random()->value());
            $subnetUUidFloatGroup = new UuidFloatgroup($subnet->floatgroupUuid());
            $subnetMask = new SubnetMask($subnet->mask());
            $subnetIP = new SubnetIP($subnet->ip());
            $ip4Subnet = new Ip4SubnetVo(
                $subnetUUid,  $subnetUUidFloatGroup,
                $subnetMask,  $subnetIP
            );

            $result = $this->subnetService->__invoke($ip4Subnet);
            
            
        } catch ( \Exception $e){
            $result = $e;
        }

        return new CreatedSubnetResponse($result);
    }

}