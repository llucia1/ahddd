<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4\Application\Cqrs\Handlers;


use GridCP\Common\Domain\Bus\Query\QueryHandler;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4Entity;
use GridCP\Net\Ip4\Application\Cqrs\Queries\GetIpsByArrayNetworkIdsQueried;
use GridCP\Net\Common\Application\Helpers\Ip4Trait;
use GridCP\Net\Ip4\Application\Response\Ip4EntityResponse;
use GridCP\Net\Ip4\Application\Response\Ip4Response;
use GridCP\Net\Ip4\Application\Response\Ip4sResponse;
use GridCP\Net\Ip4\Application\Response\Ip4WithNetworkResponse;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;




use GridCP\Net\Ip4\Domain\Repository\IIp4Repository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

#[AsMessageHandler]
final readonly class GetIpsByArrayNetworkIdsQueriedHandler implements QueryHandler
{
    use Ip4Trait;
    public function __construct(private  readonly  IIp4Repository $ip4Repository)
    {
    }

    public function __invoke(GetIpsByArrayNetworkIdsQueried $networks)
    {
        try {
            $ips = $this->ip4Repository->getIpsByNetworkIds($networks->networks() );
            if (empty($ips)) {
                return new Ip4sResponse();
            }
            $response = array_map($this->toResponse(), $ips);

            return new Ip4sResponse(...$response);
            

        }catch(\Exception $ex){
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'General error' . $ex->getMessage());

        }
    }
    private function toResponse(): callable
    {
        return static fn (Ip4Entity $ip4): Ip4Response => new Ip4Response(
            $ip4->uuid,
            $ip4->ip,
            $ip4->getNetwork() ? new Ip4WithNetworkResponse(
                $ip4->getNetwork()->getUuid(),
                $ip4->getNetwork()->getName()
            ) : null,
            $ip4->active,
            $ip4->priority,
            self::getOnlyActiveTag($ip4)
        );
    }
}