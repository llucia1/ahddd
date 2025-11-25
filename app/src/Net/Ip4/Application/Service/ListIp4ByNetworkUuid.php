<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4\Application\Service;

use GridCP\Common\Domain\Bus\Query\QueryBus;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4Entity;
use GridCP\Net\Ip4\Application\Response\Ip4Response;
use GridCP\Net\Ip4\Domain\Exceptions\ListIp4EmptyException;
use GridCP\Net\Ip4\Domain\Repository\IIp4Repository;
use GridCP\Net\Ip4\Application\Response\Ip4sResponse;
use GridCP\Net\Ip4\Application\Response\Ip4WithNetworkResponse;
use GridCP\Net\Ip4\Domain\Exceptions\NerworkNoExistException;

use GridCP\Net\Ip4\Domain\VO\Ip4UuidNetwork;
use function Lambdish\Phunctional\map;

use GridCP\Net\Ip4\Domain\Service\IListIp4ByNetworkUuid;
use GridCP\Net\Ip4Network\Application\Cqrs\Queries\GetNetworkByUuidQueried;
use GridCP\Net\Ip4Tag\Application\Response\Ip4TagResponse;
use Psr\Log\LoggerInterface;

class ListIp4ByNetworkUuid implements IListIp4ByNetworkUuid
{
    public function __construct(
                                    private readonly IIp4Repository $ip4Repository,
                                    public LoggerInterface       $logger,
                                    private QueryBus             $queryBus,
                                )
    {
    }

    public function __invoke(Ip4UuidNetwork $netWorkUuid): Ip4sResponse
    {
        return $this->getAllByNetworkUuid($netWorkUuid);
    }

    public function getAllByNetworkUuid(Ip4UuidNetwork $networkUuid):Ip4sResponse
    {
        $this->logger->info("Service - Start Get all IP4 By Netwok Uuid");
        
        try {
            $responseQuery = $this->queryBus->ask(new GetNetworkByUuidQueried($networkUuid->value()));
        }catch(\Exception $ex){
            throw new NerworkNoExistException();
        }

        $this->logger->info("Service - Get one Netwok By Uuid: ".$responseQuery->get()->id()." with uuid ".$responseQuery->get()->uuid());
        $ip4s = $this->ip4Repository->findAllByNetworkid($responseQuery->get()->id());

        return empty($ip4s)
            ? throw new ListIp4EmptyException()
            : new Ip4sResponse(...map($this->toResponse(), $ip4s));
    }

    public function toResponse(): callable
    {
        return static fn (Ip4Entity $ip4): Ip4Response => new Ip4Response(
            $ip4->getUuid(),
            $ip4->getIp(),
            $ip4->getNetwork() ? new Ip4WithNetworkResponse(
                $ip4->getNetwork()->getUuid(),
                $ip4->getNetwork()->getName()
            ) : null,
            null,

            $ip4->getPriority(),
            $ip4->getActiveTag() ? new Ip4TagResponse($ip4->getActiveTag()->getUuid(), $ip4->getActiveTag()->getTag(), null) : null,
        );
    }
}