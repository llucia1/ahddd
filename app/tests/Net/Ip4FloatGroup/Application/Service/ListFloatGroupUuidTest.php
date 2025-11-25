<?php
declare(strict_types=1);

namespace Net\Ip4FloatGroup\Application\Service;

use Faker\Factory as FakerFactory;
use Faker\Generator;
use GridCP\Net\Ip4\Application\Response\Ip4Response;
use GridCP\Net\Ip4\Application\Service\ListIp4ByUuidService;
use GridCP\Net\Ip4\Domain\Exceptions\ListIp4EmptyException;

use GridCP\Net\Ip4\Infrastructure\DB\MySQL\Repository\Ip4Repository;
use GridCP\Net\Ip4FloatGroup\Application\Responses\FloatGroupResponse;
use GridCP\Net\Ip4FloatGroup\Application\Service\FloatGroupByUUIDService;
use GridCP\Net\Ip4FloatGroup\Domain\Exception\ListFloatGroupEmptyException;
use GridCP\Net\Ip4FloatGroup\Infrastructure\DB\MySQL\Entity\Ip4FloatGroupEntity;
use GridCP\Net\Ip4FloatGroup\Infrastructure\DB\MySQL\Repository\IpFloatGroupsRepository;
use GridCP\Net\Ip4Network\Infrastructure\DB\MySQL\Entity\Ip4NetworkEntity;
use PHPUnit\Framework\TestCase;
use Doctrine\Common\Collections\ArrayCollection;
class ListFloatGroupUuidTest extends TestCase
{
    protected IpFloatGroupsRepository $ip4Repository;
    protected Generator $faker;

    protected FloatGroupByUUIDService $listIp4ByUuid;
    
    public function setUp(): void
    {
        $this->ip4Repository = $this->getMockBuilder(IpFloatGroupsRepository::class)->disableOriginalConstructor()->getMock();
        $this->listIp4ByUuid = new FloatGroupByUUIDService($this->ip4Repository);
        $this->faker = FakerFactory::create();
    }
    
    public function testIpSearchByUUIDNotExist():void
    {
        $this->ip4Repository->expects($this->any())
            ->method('getByUuid')
            ->willReturn(null);

        $this->expectException(ListFloatGroupEmptyException::class);
        $this->listIp4ByUuid->__invoke($this->faker->uuid());
    }

    public function testIpSearchByUUIDOK():void
    {
        $uuidFloatgroup = $this->faker->uuid();
        $ip4Entity = new Ip4FloatGroupEntity();
        $ip4Entity->setUuid($uuidFloatgroup);
        $ip4Entity->setName($this->faker->name());
        $ip4Entity->setActive($this->faker->boolean());

        $netwok1 = new Ip4NetworkEntity();
        $netwok1->setUuid($this->faker->uuid());
        $netwok1->setName($this->faker->name());

        $netwok2 = new Ip4NetworkEntity();
        $netwok2->setUuid($this->faker->uuid());
        $netwok2->setName($this->faker->name());
        $networksCollection = new ArrayCollection();


        $networksCollection->add($netwok1);
        $networksCollection->add($netwok2);



        $ip4Entity->setNetworks($networksCollection);


        $this->ip4Repository->expects($this->any())
            ->method('getByUuidWithNetworks')
            ->with($uuidFloatgroup)
            ->willReturn($ip4Entity);
        $result = $this->listIp4ByUuid->__invoke($ip4Entity->getUuid());
        $this->assertNotEmpty($result->networks());
        $this->assertArrayHasKey('uuid', $result->networks()[0]);
        $this->assertArrayHasKey('name', $result->networks()[0]);
        $this->assertInstanceOf(FloatGroupResponse::class , $result);
        $this->assertEquals($ip4Entity->getUuid(), $result->uuid());
        $this->assertEquals($ip4Entity->getName(), $result->name());
        $this->assertEquals($ip4Entity->isActive(), $result->active());
        $this->assertCount(2, $result->networks());
        
    }
}