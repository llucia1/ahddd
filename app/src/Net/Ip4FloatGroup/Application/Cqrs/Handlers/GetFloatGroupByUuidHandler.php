<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4FloatGroup\Application\Cqrs\Handlers;


use GridCP\Common\Domain\Bus\Query\QueryHandler;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;


use GridCP\Net\Ip4FloatGroup\Application\Cqrs\Queries\GetFloatGroupByUuidQueried;
use GridCP\Net\Ip4FloatGroup\Application\Responses\FloatGroupResponse;
use GridCP\Net\Ip4FloatGroup\Application\Service\FloatGroupByUUIDService;
use GridCP\Net\Ip4FloatGroup\Domain\Exception\ListFloatGroupEmptyException;

#[AsMessageHandler]
final readonly class GetFloatGroupByUuidHandler implements QueryHandler
{


    public function __construct(private  readonly  FloatGroupByUUIDService $floatGroupService)
    {
    }


    public function __invoke(GetFloatGroupByUuidQueried $floatGroup): FloatGroupResponse
    {
        try {
            return $this->floatGroupService->__invoke( $floatGroup->uuid() );
        }catch(\Exception $ex){
            throw new ListFloatGroupEmptyException();
        }
    }
}