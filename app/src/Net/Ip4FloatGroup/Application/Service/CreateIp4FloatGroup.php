<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4FloatGroup\Application\Service;

use GridCP\Net\Ip4FloatGroup\Domain\Exception\IP4FloatGroupDuplicated;
use GridCP\Net\Ip4FloatGroup\Domain\Repository\IIp4FloatGroupRepository;
use GridCP\Net\Ip4FloatGroup\Domain\Service\ICreateIp4FloatGroup;
use GridCP\Net\Ip4FloatGroup\Domain\VO\Ip4FloatGroups;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4FloatGroupEntity;

class CreateIp4FloatGroup implements ICreateIp4FloatGroup
{
    public function __construct(
        readonly private IIp4FloatGroupRepository $ip4FloatGroupRepository,
    )
    {
    }

    public function __invoke(Ip4FloatGroups $ip4FloatGroup ): string
    {
        return $this->createIpFloatGroup($ip4FloatGroup);
    }

    public function createIpFloatGroup(Ip4FloatGroups $ip4FloatGroup):string
    {
        $existingFloatGroup = $this->ip4FloatGroupRepository->getByName($ip4FloatGroup->Name()->value());

        if ($existingFloatGroup) {
            throw new IP4FloatGroupDuplicated($existingFloatGroup->getName());
        }

        $floatGroupEntity = new Ip4FloatGroupEntity();
        $floatGroupEntity->setUuid($ip4FloatGroup->Uuid()->value());
        $floatGroupEntity->setName($ip4FloatGroup->Name()->value());
        $floatGroupEntity->setActive($ip4FloatGroup->Active()->value());
        $this->ip4FloatGroupRepository->save($floatGroupEntity);
        return $floatGroupEntity->getUuid();
    }
}