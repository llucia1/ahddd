<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Tag\Application\Service;

use GridCP\Net\Ip4\Domain\Repository\IIp4Repository;
use GridCP\Net\Ip4Tag\Domain\Exception\Ip4NotExits;
use GridCP\Net\Ip4Tag\Domain\Exception\Ip4TagDuplicated;
use GridCP\Net\Ip4Tag\Domain\Repository\IIp4TagRepository;
use GridCP\Net\Ip4Tag\Domain\Service\ICreateIp4Tag;
use GridCP\Net\Ip4Tag\Domain\VO\Ip4Tag;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4TagEntity;

class CreateIP4Tag implements ICreateIp4Tag
{
    public function __construct(
        readonly private IIp4TagRepository $ip4TagRepository,
        readonly private IIp4Repository $ip4Repository,
    )
    {
    }

    public function __invoke(Ip4Tag $ip4Tag): string
    {
        return $this->createIp4Tag($ip4Tag);
    }

    public function createIp4Tag(Ip4Tag $ip4Tag): string
    {
        $ip = $this->ip4Repository->findByUuid($ip4Tag->uuidIp()->value());
        if (!$ip) { 
            throw new Ip4NotExits();
        }
        $tag = $this->ip4TagRepository->findById($ip->getId());
        if ($tag) {
            throw new Ip4TagDuplicated();
        }

        $tagEntity = new Ip4TagEntity();
        $tagEntity->setUuid($ip4Tag->Uuid()->value());
        $tagEntity->setTag($ip4Tag->Tag()->value());
        $tagEntity->setIp($ip);
        $this->ip4TagRepository->save($tagEntity);
        return $tagEntity->getUuid();
    }

}