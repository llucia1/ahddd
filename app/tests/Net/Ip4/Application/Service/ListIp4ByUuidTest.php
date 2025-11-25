<?php
declare(strict_types=1);

namespace Tests\Net\Ip4\Application\Service;

use Faker\Factory as FakerFactory;
use Faker\Generator;
use GridCP\Net\Ip4\Application\Response\Ip4Response;
use GridCP\Net\Ip4\Application\Response\Ip4WithNetworkResponse;
use GridCP\Net\Ip4\Application\Service\ListIp4ByUuidService;
use GridCP\Net\Ip4\Domain\Exceptions\Ip4NoFoundException;
use GridCP\Net\Ip4\Domain\Exceptions\ListIp4EmptyException;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4Entity;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4NetworkEntity;
use GridCP\Net\Ip4\Domain\VO\Ip4Uuid;
use GridCP\Net\Ip4\Infrastructure\DB\MySQL\Repository\Ip4Repository;
use PHPUnit\Framework\TestCase;

class ListIp4ByUuidTest extends TestCase
{
    protected Ip4Repository $ip4Repository;
    protected Generator $faker;

    protected ListIp4ByUuidService $listIp4ByUuid;

    public function setUp(): void
    {
        $this->ip4Repository = $this->getMockBuilder(Ip4Repository::class)->disableOriginalConstructor()->getMock();
        $this->listIp4ByUuid = new ListIp4ByUuidService($this->ip4Repository);
        $this->faker = FakerFactory::create();
    }

    public function testIpSearchByUUIDNotExist():void
    {
        $uuid = new Ip4Uuid( $this->faker->uuid);
        $this->ip4Repository->expects($this->any())
            ->method('findByUuid')
            ->with($uuid->value())

            ->willReturn(null);

        $this->expectException(Ip4NoFoundException::class);
        $this->listIp4ByUuid->__invoke($uuid );
    }
/* php bin/phpunit tests/Net/Ip4/Application/Service/ListIp4ByUuidTest.php
php bin/phpunit tests/Net/Ip4/Presentation/Rest/V1/E2ECreateIp4Test.php */
    public function testIpSearchByUUIDOK():void
    {
        $uuid = new Ip4Uuid( $this->faker->uuid);
        $ip4Entity = new Ip4Entity();
        $ip4Entity->setUuid($this->faker->uuid());
        $ip4Entity->setIp($this->faker->ipv4());
        $networkEntity = new Ip4NetworkEntity();
        $networkEntity->setUuid($uuid->value());
        $networkEntity->setName($this->faker->name());
        $ip4Entity->setNetwork ($networkEntity);
        $ip4Entity->setActive(true);
        
        $this->ip4Repository->expects($this->any())
            ->method('findByUuid')
            ->with($uuid->value())
            ->willReturn($ip4Entity);
        $result = $this->listIp4ByUuid->__invoke($uuid);



        $this->assertInstanceOf(Ip4Response::class , $result);
        $this->assertInstanceOf(Ip4WithNetworkResponse::class , $result->network());

        $this->assertEquals($result->network()->uuid(), $ip4Entity->getNetwork()->getUuid());
        $this->assertEquals($result->network()->name(), $ip4Entity->getNetwork()->getName());

    }
}