<?php

declare(strict_types=1);

namespace GridCP\Net\Ip4\Application\Service;

use GridCP\Common\Domain\Bus\Query\QueryBus;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4NetworkEntity;
use GridCP\Net\Ip4\Application\Response\Ip4Response;
use GridCP\Net\Ip4\Application\Response\Ip4WithNetworkResponse;
use GridCP\Net\Ip4\Domain\Exceptions\ListIp4EmptyException;
use GridCP\Net\Ip4\Domain\VO\Ip4;


use GridCP\Net\Ip4\Infrastructure\DB\MySQL\Repository\Ip4Repository;
use GridCP\Net\Ip4\Domain\Service\ICreateIp4Service;
use Psr\Log\LoggerInterface;
use GridCP\Net\Common\Application\Helpers\CalcIps;
use GridCP\Net\Ip4\Domain\Exceptions\Ip4Duplicated;
use GridCP\Net\Ip4\Domain\Exceptions\Ip4ErrorInsertBD;
use GridCP\Net\Ip4\Domain\Exceptions\NerworkNoExist;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4Entity;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4TagEntity;
use GridCP\Net\Ip4\Domain\Exceptions\Ip4AreAsignnedException;
use GridCP\Net\Ip4\Domain\Exceptions\Ip4sInSubnetsException;
use GridCP\Net\Ip4\Domain\Exceptions\MaskNotValid;
use GridCP\Proxmox\Vm\Application\Helpers\IpsTrait;
use IPCalc\IP;

class CreateIP4 implements ICreateIp4Service
{
    use CalcIps, IpsTrait;
    public function __construct(
                                    private readonly Ip4Repository $ip4Repository,
                                    private  readonly  LoggerInterface $logger,
                                    private QueryBus             $queryBus
                                )
    {
    }

    public function __invoke(Ip4 $ip4): array
    {
        return $this->createIP4($ip4);
    }

    public function createIP4(Ip4 $ip4): array
    {
        $this->logger->info("Start Service: creating a new IP4 record: " . $ip4->Ip4()->value());
        $network = $this->ip4Repository->existIdNetwork( $ip4->UuidNetwork()->value() );
        if (!$network) {
            throw new NerworkNoExist($ip4->UuidNetwork()->value());
        }
        $ipCal = new IP($ip4->Ip4()->value());
        if ($ipCal->getCidr() < 20) {
            throw new MaskNotValid(''.$ipCal->getCidr());
        }
        $ip4s = $this->getIp4s($ipCal);
        $results = $this->saveAll($ip4s, $network, $ip4->Priority()->value(), $ip4->Tag()->value());
        return empty($ip4s)
            ? throw new ListIp4EmptyException()
            : $this->toResponse($results);
    }

    public function toResponse(array $ip4sEntity):?array
    {
        $result = [];
        foreach ($ip4sEntity as $ip4) {
            $result[] = new Ip4Response(
                                            $ip4->getUuid(),
                                            $ip4->getIp(),
                                            new Ip4WithNetworkResponse(
                                                                                    $ip4->getNetwork()->getUuid(),
                                                                                    $ip4->getNetwork()->getName()
                                                                                ),
                                            $ip4->isActive()
                                        );
        }
        return $result;
    }

    public function saveAll(array $ip4s, Ip4NetworkEntity $network, int $priority, ?string $tag): array
    {
        $result = [];
        foreach ($ip4s as $ip4) {
            try {
                $existingIp = $this->ip4Repository->findByIP( $ip4);
                if ($existingIp) {
                    throw new Ip4Duplicated($ip4);
                }
                
                $ip4Entity = new Ip4Entity();
                $ip4Entity->setNetwork($network);
                $ip4Entity->setIp($ip4);
                $ip4Entity->setActive(true);
                $ip4Entity->setPriority($priority);



                $tagEntity = new Ip4TagEntity();
                $tagEntity->setTag($tag);
                $tagEntity->setActive(true);

                $ip4Entity->addTag($tagEntity);
                $this->ip4Repository->save($ip4Entity);
    
                $result[] = $ip4Entity;
            } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
                throw new Ip4Duplicated($ip4);
            } catch (\Exception $e) {
                throw new Ip4ErrorInsertBD($ip4, $e->getMessage());
            }

        }
        return $result;
    }



}