<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Tag\Application\Service;

use GridCP\Net\Ip4Tag\Application\Response\Ip4TagResponse;
use GridCP\Net\Ip4Tag\Domain\Exception\Ip4NotExits;
use GridCP\Net\Ip4Tag\Domain\Repository\IIp4TagRepository;
use GridCP\Net\Ip4Tag\Domain\Service\IGetIp4Tag;
use GridCP\Net\Ip4Tag\Domain\VO\Ip4TagUuid;

class GetIP4TagsService implements IGetIp4Tag
{
    public function __construct(
        readonly private IIp4TagRepository $ip4TagRepository,
    )
    {
    }

    public function __invoke(Ip4TagUuid $ip4Tag): Ip4TagResponse
    {
        return $this->getIp4Tag($ip4Tag);
    }

    public function getIp4Tag(Ip4TagUuid $ip4Tag):Ip4TagResponse
    {
        $ipTag = $this->ip4TagRepository->findByUuidWithIp($ip4Tag->value());
        if (!$ipTag) { 
            throw new Ip4NotExits();
        }
        
        return new Ip4TagResponse(
            uuid: $ipTag->getUuid(),
            tag: $ipTag->getTag(),
            ip4: $ipTag->getIp()
        );
    }

}