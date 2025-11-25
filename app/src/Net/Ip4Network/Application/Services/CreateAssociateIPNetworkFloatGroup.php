<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Network\Application\Services;

use GridCP\Common\Domain\Bus\Query\QueryBus;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4FloatGroupEntity;
use GridCP\Net\Ip4FloatGroup\Application\Cqrs\Queries\GetFloatGroupByUuidQueried;
use GridCP\Net\Ip4Network\Application\Responses\FloatGroupQueryResponse;
use GridCP\Net\Ip4Network\Domain\Exception\ErrorFloatGroupNotExist;
use GridCP\Net\Ip4Network\Domain\Exception\ErrorNetworkNotExist;
use GridCP\Net\Ip4Network\Domain\Services\IAssociateNetworkFloatGroup;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4NetworkFloatGroupEntity;
use GridCP\Net\Ip4FloatGroup\Application\Cqrs\Queries\GetFloatGroupEntityByUuidQueried;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkUUID;
use GridCP\Net\Ip4Network\Domain\VO\FloatGroupUuuid;
use GridCP\Net\Ip4Network\Infrastructure\DB\MySQL\Repository\Ip4NetworkRepository;

use GridCP\Net\Ip4Network\Infrastructure\DB\MySQL\Repository\Ip4NetworkFloatGroupRepository;
use Psr\Log\LoggerInterface;

class CreateAssociateIPNetworkFloatGroup implements IAssociateNetworkFloatGroup
{
    public function __construct(
        readonly private Ip4NetworkFloatGroupRepository $networkFloatGroupRepository,
        readonly private Ip4NetworkRepository $networkRepository,
        public LoggerInterface       $logger,
        private QueryBus             $queryBus,
    )
    {
    }
    
    public function __invoke(Ip4NetworkUUID $networkUuid, FloatGroupUuuid $floatGroupUuid ): void
    {
        $this->associate($networkUuid, $floatGroupUuid);
    }

    public function associate(Ip4NetworkUUID $networkUuid, FloatGroupUuuid $floatGroupUuid):void
    {
       $floatGroup = $this->getFloatGroupByUuid($floatGroupUuid);
       if (!$floatGroup) {
            $this->logger->info("Error float Group Not Exits -> ".$floatGroupUuid->value());
            throw new ErrorFloatGroupNotExist($floatGroupUuid->value());
       }
       $network = $this->networkRepository->getByUuid($networkUuid->value());
       if (!$network) {
            $this->logger->info("Error Network Not Exits -> ".$networkUuid->value());
            throw new ErrorNetworkNotExist($networkUuid->value());
        }
        $associated = $this->networkFloatGroupRepository->getByIdNetwork($network->getId());

        $networkFloatGroupEntity = new Ip4NetworkFloatGroupEntity();
        $networkFloatGroupEntity->setFloatGroup( $floatGroup );
        $networkFloatGroupEntity->setNetwork($network);
        $networkFloatGroupEntity->setActive(true);
        if (!$associated) {
            $this->logger->info("New Associte Network Floatgroup.");
            $this->networkFloatGroupRepository->save($networkFloatGroupEntity);
        } else {
            $this->logger->info("Update Associte Network Floatgroup.");
            if ( $associated->getId() !== $floatGroup->getId() )
            {
                $associated->setActive(false);
                $this->networkFloatGroupRepository->save($associated);
                $this->networkFloatGroupRepository->save($networkFloatGroupEntity);
            }
        }
        
    }


    public function getFloatGroupByUuid(FloatGroupUuuid $floatGroupUuid): ?Ip4FloatGroupEntity
    {
        try {
            $fg = $this->queryBus->ask(new GetFloatGroupEntityByUuidQueried($floatGroupUuid->value()));
            return $fg->get();
        }catch(\Exception $ex){
            $this->logger->info("Error float Group Not Found -> ".$floatGroupUuid->value());
            throw new ErrorFloatGroupNotExist($floatGroupUuid->value());
        }
        
    }
}