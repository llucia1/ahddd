<?php
declare(strict_types=1);

namespace Net\Ip4Network\Application\Services;


use Faker\Factory as FakerFactory;
use Faker\Generator;

use GridCP\Common\Domain\Bus\Query\QueryBus;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4FloatGroupEntity;
use GridCP\Net\Ip4FloatGroup\Application\Cqrs\Queries\GetFloatGroupByUuidQueried;
use GridCP\Net\Ip4Network\Application\Responses\FloatGroupQueryResponse;
use GridCP\Net\Ip4Network\Application\Services\CreateAssociateIPNetworkFloatGroup;

use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4NetworkEntity;
use GridCP\Net\Ip4Network\Infrastructure\DB\MySQL\Repository\Ip4NetworkRepository;
use GridCP\Net\Ip4Network\Domain\Exception\ErrorFloatGroupNotExist;
use GridCP\Net\Ip4Network\Domain\Exception\ErrorNetworkNotExist;

use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4NetworkFloatGroupEntity;
use GridCP\Net\Ip4FloatGroup\Application\Cqrs\Queries\GetFloatGroupEntityByUuidQueried;
use GridCP\Net\Ip4FloatGroup\Application\Responses\FloatGroupEntityResponse;
use GridCP\Net\Ip4Network\Infrastructure\DB\MySQL\Repository\Ip4NetworkFloatGroupRepository;
use phpDocumentor\Reflection\Types\Void_;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkUUID;
use GridCP\Net\Ip4Network\Domain\VO\FloatGroupUuuid;

class CreateAssociateIPNetworkFloatGroupTest extends  TestCase
{

    const TXT_NETWORK_NOT_EXITS = "Error Network Not Exist -> ";
    protected Ip4NetworkRepository $ip4NetworkRepository;
    protected Ip4NetworkFloatGroupRepository $ip4NetworkFloatRepository;
    protected LoggerInterface $logger;
    protected QueryBus $queryBusMock;
    protected Ip4NetworkUUID $networkUuid;
    protected FloatGroupUuuid $floatGroupUuid;

    protected Ip4NetworkEntity $ip4NetworkEntity;
    protected Ip4NetworkFloatGroupEntity $ip4NetworkFloatGroupEntity;
    protected Ip4FloatGroupEntity $floatGroupEntity;
    protected FloatGroupQueryResponse $floatGroupQueryReponse;

    private CreateAssociateIPNetworkFloatGroup $associate;
    private Generator $faker;
    private FloatGroupEntityResponse $mockFloatGroupResponse;
    public function setUp(): void
    {
        $this->ip4NetworkRepository = $this->getMockBuilder(Ip4NetworkRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->ip4NetworkFloatRepository = $this->getMockBuilder(Ip4NetworkFloatGroupRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->logger = $this->createMock(LoggerInterface::class);
        $this->queryBusMock = $this->createMock(QueryBus::class);
        
        $this->faker = FakerFactory::create();
        $this->networkUuid = new Ip4NetworkUUID($this->faker->uuid());
        $this->floatGroupUuid = new FloatGroupUuuid($this->faker->uuid());


        $networkId = 1;
        $this->ip4NetworkEntity = new Ip4NetworkEntity();
        $this->ip4NetworkEntity->setId((int)$networkId);
        $this->ip4NetworkEntity->setUuid($this->networkUuid->value());
        $this->ip4NetworkEntity->setName("Mock");
        $this->ip4NetworkEntity->setActive(false);

        $idFloatGroup = 2;
        $this->floatGroupEntity = new Ip4FloatGroupEntity();
        $this->floatGroupEntity->setId((int)$idFloatGroup);
        $this->floatGroupEntity->setUuid($this->floatGroupUuid->value());
        $this->floatGroupEntity->setName("Ubrique");
        $this->floatGroupEntity->setActive(true);



        $this->ip4NetworkFloatGroupEntity = new Ip4NetworkFloatGroupEntity();
        $this->ip4NetworkFloatGroupEntity->setFloatgroup($this->floatGroupEntity);
        $this->ip4NetworkFloatGroupEntity->setNetwork($this->ip4NetworkEntity);
        $this->ip4NetworkFloatGroupEntity->setActive(true);

        $this->floatGroupQueryReponse = new FloatGroupQueryResponse(
            $this->floatGroupEntity->getUuid(),
            $this->floatGroupEntity->getName(),
            $this->floatGroupEntity->isActive(),
            $this->floatGroupEntity->getId()
        );

        $this->associate = new CreateAssociateIPNetworkFloatGroup(
            $this->ip4NetworkFloatRepository,
            $this->ip4NetworkRepository,
            $this->logger,
            $this->queryBusMock
        );
        $this->mockFloatGroupResponse = new FloatGroupEntityResponse($this->floatGroupEntity);
    }

    private function getByUuidRepository(?Ip4NetworkEntity $return): void
    {
        $this->ip4NetworkRepository->expects($this->any())
            ->method('getByUuid')
            ->with($this->networkUuid->value())
            ->willReturn($return);
    }
    private function getByIdNetworkNetworkFloatGroup(?Ip4NetworkFloatGroupEntity $return): void
    {
        $this->ip4NetworkFloatRepository->expects($this->any())
            ->method('getByIdNetwork')
            ->with($this->ip4NetworkEntity->getId())
            ->willReturn($return);
    }
    private function getFloatGroupByUuidCqrs(?FloatGroupEntityResponse $mockFloatGroupResponse): void
    {
        $this->queryBusMock->expects($this->once())
                 ->method('ask')
                 ->with($this->isInstanceOf(GetFloatGroupEntityByUuidQueried::class))
                 ->willReturn($mockFloatGroupResponse);  

    }

// php bin/phpunit tests/Net/Ip4Network/Application/Services/CreateAssociateIPNetworkFloatGroupTest.php

    public function testCreateNewAssociateNetworkFloatGroupSuccess(): void
    {

        $this->getFloatGroupByUuidCqrs($this->mockFloatGroupResponse);  

        $this->getByUuidRepository($this->ip4NetworkEntity);  

        $this->ip4NetworkFloatRepository->expects($this->once())
            ->method('getByIdNetwork')
            ->with($this->ip4NetworkEntity->getId())
            ->willReturn(null); 

        $this->ip4NetworkFloatRepository->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Ip4NetworkFloatGroupEntity::class)); 

        $this->associate->__invoke($this->networkUuid, $this->floatGroupUuid);
    }   

    public function testErrorWhenNetworkNoExist(): void
    {
        $this->getFloatGroupByUuidCqrs($this->mockFloatGroupResponse);    

        $this->getByUuidRepository(null);  


        $this->expectException(ErrorNetworkNotExist::class);
        $this->expectExceptionMessage("Error Network Not Exist -> " . $this->networkUuid->value()); 

        $this->associate->__invoke($this->networkUuid, $this->floatGroupUuid);
    }   

    public function testErrorWhenFloatGroupNotExist(): void
    {
        $this->queryBusMock->expects($this->once())
            ->method('ask')
            ->willThrowException(new ErrorFloatGroupNotExist($this->floatGroupUuid->value()));  

        $this->expectException(ErrorFloatGroupNotExist::class);
        $this->associate->__invoke($this->networkUuid, $this->floatGroupUuid);
    }

}
