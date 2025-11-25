<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4FloatGroup\Application\Cqrs\Handlers;


use GridCP\Common\Domain\Bus\Query\QueryHandler;

use GridCP\Net\Ip4FloatGroup\Application\Cqrs\Queries\GetFloatGroupEntityByUuidQueried;
use GridCP\Net\Ip4FloatGroup\Application\Responses\FloatGroupEntityResponse;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use GridCP\Net\Ip4FloatGroup\Domain\Repository\IIp4FloatGroupRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

#[AsMessageHandler]
final readonly class GetFloatGroupEntityByUuidHandler implements QueryHandler
{


    public function __construct(private  readonly  IIp4FloatGroupRepository $floatGroupService)
    {
    }

    public function __invoke(GetFloatGroupEntityByUuidQueried $floatGroup): FloatGroupEntityResponse
    {
        try {
            $fg = $this->floatGroupService->getByUuid( $floatGroup->uuid() );
            return new FloatGroupEntityResponse($fg);
        }catch(\Exception $ex){
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'General error' . $ex->getMessage());

        }
    }
}