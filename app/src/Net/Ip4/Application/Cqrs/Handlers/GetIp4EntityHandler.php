<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4\Application\Cqrs\Handlers;


use GridCP\Common\Domain\Bus\Query\QueryHandler;
use GridCP\Net\Ip4\Application\Cqrs\Queries\GetIp4EntityQueried;
use GridCP\Net\Ip4\Application\Response\Ip4EntityResponse;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;




use GridCP\Net\Ip4\Domain\Repository\IIp4Repository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

#[AsMessageHandler]
final readonly class GetIp4EntityHandler implements QueryHandler
{
    public function __construct(private  readonly  IIp4Repository $ip4Repository)
    {
    }

    public function __invoke(GetIp4EntityQueried $ip): ?Ip4EntityResponse
    {
        try {
            $ip = $this->ip4Repository->findByIPWhitRelationsNetworksTags($ip->ip() );
            return new Ip4EntityResponse($ip);

        }catch(\Exception $ex){
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'General error' . $ex->getMessage());

        }
    }

}