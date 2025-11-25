<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Network\Application\Cqrs\Handlers;


use GridCP\Common\Domain\Bus\Query\QueryHandler;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

use GridCP\Net\Ip4Network\Application\Cqrs\Queries\GetNetworkByUuidQueried;
use GridCP\Net\Ip4Network\Application\Responses\Ip4NetworkQueryResponse;
use GridCP\Net\Ip4Network\Application\Responses\Ip4NetworkResponse;
use GridCP\Net\Ip4Network\Application\Services\ListIP4NetworkByUUIDService;
use GridCP\Net\Ip4Network\Domain\Exception\ListIp4NetworkEmptyException;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkUUID;

#[AsMessageHandler]
final readonly class GetNetworkByUuidQueriedHandler implements QueryHandler
{


    public function __construct(private  readonly  ListIP4NetworkByUUIDService $networkByUuidService)
    {
    }


    public function __invoke(GetNetworkByUuidQueried $network): Ip4NetworkQueryResponse
    {
        try {
            $ipNetwork = $this->networkByUuidService->__invoke( new Ip4NetworkUUID($network->uuid()) );
            return $this->toReponse($ipNetwork);
        }catch(\Exception $ex){
            throw new ListIp4NetworkEmptyException();
        }
    }


    private function toReponse(Ip4NetworkResponse $ipNetwork):Ip4NetworkQueryResponse
    {
        return new Ip4NetworkQueryResponse($ipNetwork);
    }
}