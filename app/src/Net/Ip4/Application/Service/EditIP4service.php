<?php

declare(strict_types=1);

namespace GridCP\Net\Ip4\Application\Service;

use GridCP\Common\Domain\Bus\Query\QueryBus;
use GridCP\Common\Domain\ValueObjects\Ip4TagTag;
use GridCP\Common\Domain\ValueObjects\UuidValueObject;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4NetworkEntity;


use Psr\Log\LoggerInterface;
use GridCP\Net\Common\Application\Helpers\CalcIps;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4Entity;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4TagEntity;
use GridCP\Net\Ip4\Domain\Exceptions\Ip4ErrorInsertBD;
use GridCP\Net\Ip4\Domain\Exceptions\NerworkNoExist;
use GridCP\Net\Ip4\Domain\Repository\IIp4Repository;
use GridCP\Net\Ip4\Domain\Service\IEditIp4Service;
use GridCP\Net\Ip4\Domain\VO\Ip4Priority;
use GridCP\Net\Ip4\Domain\VO\PatchIp4Vo;
use GridCP\Net\Ip4Tag\Application\Cqrs\Queries\EditTagQueried;
use IPCalc\IP;

class EditIP4service implements IEditIp4Service
{
    use CalcIps;
    public function __construct(
                                    private readonly IIp4Repository $ip4Repository,
                                    private  readonly  LoggerInterface $logger,
                                    private QueryBus             $queryBus
                                )
    {
    }

    public function __invoke(PatchIp4Vo $ip4): array
    {
        return $this->editIP4($ip4);
    }

    public function editIP4(PatchIp4Vo $ip4): array
    {
        $this->logger->info("Start Service: Editing a IP4 record: " . $ip4->Ip4()->value());
        $network =  null;
        if ($ip4->UuidNetwork()) {
            $network = $this->ip4Repository->existIdNetwork( $ip4->UuidNetwork()->value() );
            if (!$network) {
                throw new NerworkNoExist($ip4->UuidNetwork()->value());
            }
        }
        $ip4s = null;
        if ($ip4->Ip4()->value()) {
            $ipCal = new IP($ip4->Ip4()->value());
            $ip4s = $this->getIp4s($ipCal);
        }
        return $this->editAll($ip4s, $network, $ip4->Priority(), $ip4->Tag() );
    }

    public function editAll(array $ip4s, ?Ip4NetworkEntity $network, ?Ip4Priority $priority, ?Ip4TagTag $tag): array
    {
        $notFounfIp = [];
        foreach ($ip4s as $ip4) {
            try {
                $ip4Entity = $this->ip4Repository->findByIPWhitRelationsNetworksTags( $ip4);
                $ipLogicalDeletion = false;
                if (!$ip4Entity) {
                    $notFounfIp[] = $ip4;
                } else  {
                    if ($network){
                        $ipLogicalDeletion = true;
                    }
                    if ($priority){
                        $ipLogicalDeletion = true;
                    }

                    $tagNewEntity = false;
                    if ( $tag ) {
                        $ipLogicalDeletion = true;
                        $tagNewEntity = true;
                        $tagNewEntity = new Ip4TagEntity();
                        $tagNewEntity->setUuid($ip4Entity->getUuid() );
                        $tagNewEntity->setIp($ip4Entity);
                        $tagNewEntity->setTag( $tag->value() );
                        $tagNewEntity->setActive(true);
                    }
                    
                    if ($ipLogicalDeletion) {

                        $ip4EntityNew = new Ip4Entity();
                        $ip4EntityNew->setUuid($ip4Entity->getUuid());
                        $ip4EntityNew->setNetwork($ip4Entity->getNetwork());
                        $ip4EntityNew->setIp($ip4Entity->getIp());
                        $ip4EntityNew->setActive(true);
                        $ip4EntityNew->setPriority($ip4Entity->getPriority());

                        if ($tagNewEntity) {
                            $tagEntity = $ip4Entity->getActiveTag();
                            if ( $tagEntity ) {
                                $responseQuery = $this->queryBus->ask(new EditTagQueried($tagEntity->getUuid(), $ip4Entity->getUuid(), $tagEntity->getTag(), false));
                            }
                            $ip4EntityNew->addTag($tagNewEntity);
                        }


                        $ip4Entity->setActive(false);
                        $this->ip4Repository->save($ip4Entity);

                        $this->ip4Repository->save($ip4EntityNew);
                    }
                }
            } catch (\Exception $e) {
                throw new Ip4ErrorInsertBD($ip4, $e->getMessage());
            }

        }
        return $notFounfIp;
    }
}