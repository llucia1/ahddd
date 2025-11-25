<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4\Application\Cqrs\Handlers;


use GridCP\Common\Domain\Bus\Query\QueryHandler;

use GridCP\Net\Ip4\Application\Response\Ip4sNotExitsResponses;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

use GridCP\Net\Ip4\Application\Cqrs\Queries\GetIp4sNotExitsQueried;
use GridCP\Net\Ip4\Domain\Exceptions\GetIP4Exception;
use GridCP\Net\Ip4\Domain\Repository\IIp4Repository;



#[AsMessageHandler]
final readonly class GetIp4sNotExitsHandler implements QueryHandler
{
    public function __construct(private  readonly  IIp4Repository $ip4Repository)
    {
    }

    public function __invoke(GetIp4sNotExitsQueried $ips): ?Ip4sNotExitsResponses
    {
        try {

            $ip4NotExits = array_filter($ips->ips(), function($ip) {
                return !$this->ip4Repository->findByIp($ip->value());
            });
            return $this->toResponse($ip4NotExits);

        }catch(\Exception $ex){
            throw new GetIP4Exception($ex);
        }
    }
    public function toResponse( ?array $response):?Ip4sNotExitsResponses
    {
        return new Ip4sNotExitsResponses( $response );

    }

}