<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4\Application\Cqrs\Handlers;


use GridCP\Common\Domain\Bus\Query\QueryHandler;
use GridCP\Net\Ip4\Application\Cqrs\Queries\GetIp4EntityQueried;
use GridCP\Net\Ip4\Application\Cqrs\Queries\GetIp4WithRelationsQueried;
use GridCP\Net\Ip4\Application\Response\Ip4EntityResponse;
use GridCP\Net\Ip4\Application\Response\Ip4sNotExitsResponses;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;




use GridCP\Net\Ip4\Domain\Repository\IIp4Repository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

#[AsMessageHandler]
final readonly class GetIp4WithRelationsHandler implements QueryHandler
{
    public function __construct(private  readonly  IIp4Repository $ip4Repository)
    {
    }

    public function __invoke(GetIp4WithRelationsQueried $ip): ?Ip4sNotExitsResponses
    {
        try {
            $ip = $this->ip4Repository->findByIPWithRelations($ip->ip() );
            return new Ip4sNotExitsResponses($ip);

        }catch(\Exception $ex){
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'General error' . $ex->getMessage());

        }
    }

}