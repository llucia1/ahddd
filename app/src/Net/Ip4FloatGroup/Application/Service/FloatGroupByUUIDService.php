<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4FloatGroup\Application\Service;

use GridCP\Net\Ip4FloatGroup\Application\Responses\FloatGroupResponse;
use GridCP\Net\Ip4FloatGroup\Domain\Exception\ListFloatGroupEmptyException;
use GridCP\Net\Ip4FloatGroup\Domain\Repository\IIp4FloatGroupRepository;
use GridCP\Net\Ip4FloatGroup\Domain\Service\IGetFloatGroupByUUIDService;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4FloatGroupEntity;


class FloatGroupByUUIDService implements IGetFloatGroupByUUIDService
{

    public function __construct(
        readonly private IIp4FloatGroupRepository $ipFloatGroupsRepository,
    )
    {
    }

    public function __invoke(string $uuid): FloatGroupResponse
    {

     return $this->getAll($uuid);
    }

    public function getAll(string $uuid): FloatGroupResponse
    {
        $existingFloatGroup = $this->ipFloatGroupsRepository->getByUuidWithRelations($uuid);
        return  $existingFloatGroup
            ? $this->toResponse($existingFloatGroup)
            : throw new ListFloatGroupEmptyException();
    }

    public function toResponse(Ip4FloatGroupEntity $FloatGroup):FloatGroupResponse
    {
       return  new FloatGroupResponse(
           $FloatGroup->getUuid(),
           $FloatGroup->getName(),
           $FloatGroup->isActive(),
           $FloatGroup->getId(),
           $this->toResponseNetworks(
            $FloatGroup->getNetworks()->toArray()
        ),
           $this->toResponseNodes($FloatGroup->getNodeFloatGroups()->toArray())
       );
    }
    public function toResponseNetworks(array $networkRelations): array
    {
        $result = [];
        foreach ($networkRelations as $relation) {
            $network = $relation->getNetwork();
            if ($network) {
                $result[] = [
                    'uuid' => $network->getUuid(),
                    'name' => $network->getName(),
                ];
            }
        }
        return $result;
    }


    public function toResponseNodes(array $nodes): array
    {
        $result = [];
        $recommendedUuid = $this->getRecommendedNodeUuid($nodes);
    
        foreach ($nodes as $relation) {
            $node = $relation->getNode();
            if ($node) {
                $result[] = [
                    'uuid' => $node->getUuid(),
                    'gcp_node_name' => $node->getGcpName(),
                    'pve_node_name' => $node->getPveName(),
                    'priority' => $node->getPriority(),
                    'recommended' => $node->getUuid() === $recommendedUuid
                ];
            }
        }
    
        return $result;
    }

    private function getRecommendedNodeUuid(array $nodes): ?string
    {
        foreach ($nodes as $relation) {
            $node = $relation->getNode();
            if ($node) {
                return $node->getUuid();
            }
        }
    
        return null;
    }


    
}