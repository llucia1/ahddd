<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4FloatGroup\Application\Service;

use GridCP\Net\Ip4FloatGroup\Application\Responses\FloatGroupResponse;
use GridCP\Net\Ip4FloatGroup\Domain\Exception\ErrorFloatGroupNotExist;
use GridCP\Net\Ip4FloatGroup\Domain\Exception\FloatGroupHasAssociatedNetworks;
use GridCP\Net\Ip4FloatGroup\Domain\Exception\FloatGroupHasAssociatedNodes;
use GridCP\Net\Ip4FloatGroup\Domain\Exception\IP4FloatGroupDuplicated;
use GridCP\Net\Ip4FloatGroup\Domain\Repository\IIp4FloatGroupRepository;
use GridCP\Net\Ip4FloatGroup\Domain\Service\ICreateIp4FloatGroup;
use GridCP\Net\Ip4FloatGroup\Domain\Service\IDisableFloatGroup;
use GridCP\Net\Ip4FloatGroup\Domain\VO\Ip4FloatGroups;
use GridCP\Net\Ip4FloatGroup\Domain\VO\Ip4FloatGroupsUuid;
use GridCP\Net\Ip4FloatGroup\Infrastructure\DB\MySQL\Entity\Ip4FloatGroupEntity;
use Psr\Log\LoggerInterface;

class DisableFloatGroup implements IDisableFloatGroup
{
    public function __construct(
        readonly private IIp4FloatGroupRepository $ip4FloatGroupRepository,
        private LoggerInterface $logger
    )
    {
    }

    public function __invoke(Ip4FloatGroupsUuid $ip4FloatGroup ): void
    {
        $this->disableFloatGroup($ip4FloatGroup);
    }

    public function disableFloatGroup(Ip4FloatGroupsUuid $ip4FloatGroup):void
    {
        $FloatGroupEntity = $this->ip4FloatGroupRepository->getByUuidWithNetworks($ip4FloatGroup->value());

        if (!$FloatGroupEntity || !$FloatGroupEntity->isActive()){
            $this->logger->error("Error Float Group Not Exist -> " . $ip4FloatGroup->value());
            throw new ErrorFloatGroupNotExist;
        }          
        if (!empty($FloatGroupEntity->getNodeFloatGroups()->toArray())) {
            throw new FloatGroupHasAssociatedNodes();
        }      
        if (!empty($FloatGroupEntity->getNetworks()->toArray())) {
            throw new FloatGroupHasAssociatedNetworks();
        }

        
        $this->logger->info('Float Group Disable -> ' . $ip4FloatGroup->value());
        $FloatGroupEntity->setActive(false);
        $this->ip4FloatGroupRepository->save($FloatGroupEntity);

    }
}