<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4FloatGroup\Application\Service;

use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4FloatGroupEntity;
use GridCP\Net\Ip4FloatGroup\Application\Responses\FloatGroupResponse;
use GridCP\Net\Ip4FloatGroup\Application\Responses\FloatGroupResponses;
use GridCP\Net\Ip4FloatGroup\Domain\Exception\ListFloatGroupEmptyException;
use GridCP\Net\Ip4FloatGroup\Domain\Repository\IIp4FloatGroupRepository;
use GridCP\Net\Ip4FloatGroup\Domain\Service\IListFloatGroupService;
use function Lambdish\Phunctional\map;

class ListFloatGroup implements IListFloatGroupService
{
    public function __construct(private  readonly  IIp4FloatGroupRepository $floatGroupRepository){}


    public function __invoke():FloatGroupResponses
     {
      return $this->getAll();

    }
    public function getAll():FloatGroupResponses
    {
        $floatGroups =  $this->floatGroupRepository->getAllWithRelations();
        return  empty($floatGroups)
            ?throw new ListFloatGroupEmptyException()
            :new FloatGroupResponses( ...map($this->toResponse() , $floatGroups));
    }
    public function toResponse():callable
    {
        return static fn (Ip4FloatGroupEntity $floatGroup): FloatGroupResponse => new FloatGroupResponse(
            $floatGroup->getUuid(),
            $floatGroup->getName(),
            $floatGroup->isActive(),
            $floatGroup->getId(),
            array_values(array_filter(array_map(
                fn($relation) => $relation->getNetwork() ? [
                    'uuid' => $relation->getNetwork()->getUuid(),
                    'name' => $relation->getNetwork()->getName(),
                ] : null,
                $floatGroup->getNetworks()->toArray()
            ))),
            array_values(array_filter(array_map(
                fn($relation) => $relation->getNode() ? [
                    'uuid' => $relation->getNode()->getUuid(),
                    'name' => $relation->getNode()->getGcpName(),
                ] : null,
                $floatGroup->getNodeFloatGroups()->toArray()
            )))
        );
    }
}
