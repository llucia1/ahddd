<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4FloatGroup\Application\Cqrs\Handlers;


use GridCP\Common\Domain\Bus\Query\QueryHandler;
use GridCP\Net\Common\Application\Response\GetExceptionResponse;
use GridCP\Net\Ip4FloatGroup\Application\Cqrs\Queries\GetAllNetworkOfoneFloatgroupByUuidQueried;
use GridCP\Net\Ip4FloatGroup\Application\Cqrs\Queries\GetAllNodeByFloatgroupUuidQueried;
use GridCP\Net\Ip4FloatGroup\Application\Responses\AllNetworkOfFloatgroupResponse;
use GridCP\Net\Ip4FloatGroup\Application\Responses\FloatGroupResponse;
use GridCP\Net\Ip4FloatGroup\Application\Service\FloatGroupByUUIDService;
use GridCP\Net\Ip4FloatGroup\Domain\Exception\ListFloatGroupEmptyException;
use GridCP\Net\Ip4FloatGroup\Domain\Repository\IIp4FloatGroupRepository;
use GridCP\Net\Ip4FloatGroup\Infrastructure\DB\MySQL\Repository\IpFloatGroupsRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use GridCP\Net\Ip4Network\Domain\Exception\ListIp4NetworkEmptyException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

#[AsMessageHandler]
final readonly class GetAllNodeByFloatgroupUuidQueriedHandler implements QueryHandler
{


    public function __construct(private  readonly  FloatGroupByUUIDService $floatgroupservice)
    {
    }

    public function __invoke(GetAllNodeByFloatgroupUuidQueried $floatgroupUuid): FloatGroupResponse | GetExceptionResponse
    {
        try {
            $result = $this->floatgroupservice->__invoke($floatgroupUuid->uuid());
            
        }catch(ListFloatGroupEmptyException $ex){
            $result = new GetExceptionResponse( new ListFloatGroupEmptyException() );
        }catch(\Exception $ex){
            $result = new GetExceptionResponse(new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'General error' . $ex->getMessage()));
        }
        return $result;
    }
}