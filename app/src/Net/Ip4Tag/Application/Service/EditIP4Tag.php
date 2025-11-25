<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Tag\Application\Service;

use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4TagEntity;
use GridCP\Net\Ip4\Domain\Repository\IIp4Repository;
use GridCP\Net\Ip4Tag\Domain\Repository\IIp4TagRepository;
use GridCP\Net\Ip4Tag\Domain\Service\IEditIp4Tag;
use GridCP\Net\Ip4Tag\Domain\VO\Ip4Tag;
use GridCP\Net\Ip4Tag\Domain\Exception\Ip4TagNotExits;

class EditIP4Tag implements IEditIp4Tag
{
    public function __construct(
        readonly private IIp4TagRepository $ip4TagRepository,
        readonly private IIp4Repository $ip4Repository,
    )
    {
    }

    public function __invoke(Ip4Tag $ip4Tag): void
    {
        $this->editIp4Tag($ip4Tag);
    }

    public function editIp4Tag(Ip4Tag $ip4Tag): void
    {
        $tagEntity = $this->ip4TagRepository->findByuuid($ip4Tag->Uuid()->value());
        if (!$tagEntity) {
            throw new Ip4TagNotExits();
        }

        $tagEntity->setActive(false);
        $this->ip4TagRepository->save($tagEntity);
        

        $ipTagEntity = new Ip4TagEntity();
        $ipTagEntity->setUuid($ip4Tag->Uuid()->value());
        $ipTagEntity->setTag($ip4Tag->Tag() ? $ip4Tag->Tag()->value() : null );
        $ipTagEntity->setIp($tagEntity->getIp());
        $ipTagEntity->setActive( false );
        $this->ip4TagRepository->save($ipTagEntity);
    }

}

