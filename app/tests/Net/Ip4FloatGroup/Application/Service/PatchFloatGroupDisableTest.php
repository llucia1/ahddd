<?php
declare(strict_types=1);

namespace Net\Ip4FloatGroup\Application\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Faker\Factory as FakerFactory;
use Faker\Generator;

use GridCP\Common\Domain\Bus\Query\QueryBus;
use GridCP\Net\Ip4FloatGroup\Application\Service\DisableFloatGroup;
use GridCP\Net\Ip4FloatGroup\Application\Service\PatchFloatGroupService;
use GridCP\Net\Ip4FloatGroup\Domain\Exception\ErrorFloatGroupNotExist;
use GridCP\Net\Ip4FloatGroup\Domain\Exception\FloatGroupHasAssociatedNetworks;
use GridCP\Net\Ip4FloatGroup\Domain\Repository\IIp4FloatGroupRepository;
use GridCP\Net\Ip4FloatGroup\Domain\VO\Ip4FloatGroupsActive;
use GridCP\Net\Ip4FloatGroup\Domain\VO\Ip4FloatGroupsName;
use GridCP\Net\Ip4FloatGroup\Domain\VO\Ip4FloatGroupsPacth;
use GridCP\Net\Ip4FloatGroup\Domain\VO\Ip4FloatGroupsUuid;
use GridCP\Net\Ip4FloatGroup\Infrastructure\DB\MySQL\Entity\Ip4FloatGroupEntity;
use GridCP\Net\Ip4FloatGroup\Infrastructure\DB\MySQL\Entity\Ip4NetworkEntity;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class PatchFloatGroupDisableTest extends  TestCase
{

    protected IIp4FloatGroupRepository $floatGroupRepository;
    protected LoggerInterface $logger;

    private DisableFloatGroup $floatGroupDisable;
    private Generator $faker;


    public function setUp(): void
    {
        $this->floatGroupRepository = $this->getMockBuilder(IIp4FloatGroupRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
            
        $this->logger = $this->createMock(LoggerInterface::class);


        
        $this->faker = FakerFactory::create();

        $this->floatGroupDisable = new DisableFloatGroup(
            $this->floatGroupRepository,
            $this->logger,
        );    
    }

    public function testFloatGroupDisableSuccess(): void
    {

        $uuid = '92011bcd-ab2b-4100-9fe3-7c54e7f39781';
        $name = 'Madrid';
        $active = true;

        $uuidVo = new Ip4FloatGroupsUuid($uuid);

        $floatGroupEntity = new Ip4FloatGroupEntity();
        $floatGroupEntity->setUuid($uuidVo->value());
        $floatGroupEntity->setName($name);
        $floatGroupEntity->setActive($active);

        $networksCollection = new ArrayCollection();
        $floatGroupEntity->setNetworks($networksCollection);
        
        $this->floatGroupRepository->expects($this->any())
                                   ->method('getByUuidWithNetworks')
                                   ->with($uuidVo)
                                   ->willReturn($floatGroupEntity);

        $this->floatGroupRepository->expects($this->any())
             ->method('save')
             ->with($this->equalTo($floatGroupEntity));
             

        $this->floatGroupDisable->__invoke($uuidVo);
        $this->expectNotToPerformAssertions();
    }

    public function testFloatGroupDisableWithAssociateNetworksError(): void
    {

        $uuid = '92011bcd-ab2b-4100-9fe3-7c54e7f39781';
        $name = 'Madrid';
        $active = true;

        $uuidVo = new Ip4FloatGroupsUuid($uuid);

        $floatGroupEntity = new Ip4FloatGroupEntity();
        $floatGroupEntity->setUuid($uuidVo->value());
        $floatGroupEntity->setName($name);
        $floatGroupEntity->setActive($active);

        $networksCollection = new ArrayCollection();
        $network = new Ip4NetworkEntity();
        $network->setUuid($uuid);
        $network->setName('prueba');
            
        $networksCollection->add($network);
        $floatGroupEntity->setNetworks($networksCollection);
        
        $this->floatGroupRepository->expects($this->any())
                                   ->method('getByUuidWithNetworks')
                                   ->with($uuidVo)
                                   ->willReturn($floatGroupEntity);

        $this->floatGroupRepository->expects($this->any())
             ->method('save')
             ->with($this->equalTo($floatGroupEntity));
             
        $this->expectException(FloatGroupHasAssociatedNetworks::class);
        $this->floatGroupDisable->__invoke($uuidVo);
    }

    public function testFloatGroupNotExistDisableError(): void
    {

        $uuid = '92011bcd-ab2b-4100-9fe3-7c54e7f39781';
        $name = 'Madrid';
        $active = true;

        $uuidVo = new Ip4FloatGroupsUuid($uuid);

        $floatGroupEntity = new Ip4FloatGroupEntity();
        $floatGroupEntity->setUuid($uuidVo->value());
        $floatGroupEntity->setName($name);
        $floatGroupEntity->setActive($active);
            
        $networksCollection = new ArrayCollection();
        $floatGroupEntity->setNetworks($networksCollection);
        
        $this->floatGroupRepository->expects($this->any())
                                   ->method('getByUuidWithNetworks')
                                   ->with($uuidVo)
                                   ->willReturn(null);
             
        $this->expectException(ErrorFloatGroupNotExist::class);
        $this->floatGroupDisable->__invoke($uuidVo);
    }

/*
php bin/phpunit tests/Net/Ip4FloatGroup/Application/Service/PatchFloatGroupDisableTest.php
    public function testUpdateFloatGroupNotExitsKo(): void
    {

        $uuid = 'ec8f0fd1-7f3f-4ece-9f59-729824539674';
        $name = 'Madrid';
        $active = true;

        $uuidVo = new Ip4FloatGroupsUuid($uuid);  
        $nameVo = new Ip4FloatGroupsName($name);
        $activeVo =  new Ip4FloatGroupsActive($active);




        $floatGroupMock = new Ip4FloatGroupsPacth(
            $uuidVo,
            $nameVo,
            $activeVo
        );

        $floatGroupEntity = null;
        
        $this->floatGroupRepository->expects($this->any())
                                   ->method('getByUuid')
                                   ->with($floatGroupMock->uuid())
                                   ->willReturn($floatGroupEntity);

        $this->floatGroupRepository->expects($this->any())
             ->method('save')
             ->with($this->equalTo($floatGroupEntity));
             
        $this->expectException(ErrorFloatGroupNotExist::class);
        $this->update->__invoke($floatGroupMock);
        

    }
    public function testUpdateFloatGroupNotActiveKo(): void
    {

        $uuid = 'ec8f0fd1-7f3f-4ece-9f59-729824539674';
        $name = 'Madrid';
        $active = false;

        $uuidVo = new Ip4FloatGroupsUuid($uuid);  
        $nameVo = new Ip4FloatGroupsName($name);
        $activeVo =  new Ip4FloatGroupsActive($active);



        $floatGroupMock = new Ip4FloatGroupsPacth(
            $uuidVo,
            $nameVo,
            $activeVo
        );

        $floatGroupEntity = new Ip4FloatGroupEntity();
        $floatGroupEntity->setUuid($uuidVo->value());
        $floatGroupEntity->setName($nameVo->value());
        $floatGroupEntity->setActive($activeVo->value());
        

        $this->floatGroupRepository->expects($this->any())
                                   ->method('getByUuid')
                                   ->with($floatGroupMock->uuid())
                                   ->willReturn($floatGroupEntity);

        $this->floatGroupRepository->expects($this->any())
             ->method('save')
             ->with($this->equalTo($floatGroupEntity));

        $this->expectException(ErrorFloatGroupNotExist::class);
        $this->update->__invoke($floatGroupMock);

    }
    */
}