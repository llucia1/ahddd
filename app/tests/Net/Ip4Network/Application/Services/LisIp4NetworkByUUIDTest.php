<?php
declare(strict_types=1);

namespace Net\Ip4Network\Application\Services;

use Faker\Factory as FakerFactory;
use Faker\Generator;
use GridCP\Net\Ip4Network\Application\Responses\Ip4NetworkResponse;
use GridCP\Net\Ip4Network\Application\Services\ListIP4NetworkByUUIDService;
use GridCP\Net\Ip4Network\Domain\Exception\ListIp4NetworkEmptyException;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4NetworkEntity;
use GridCP\Net\Ip4Network\Infrastructure\DB\MySQL\Repository\Ip4NetworkRepository;
use PHPUnit\Framework\TestCase;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkUUID;

class LisIp4NetworkByUUIDTest extends TestCase
{

    protected Ip4NetworkRepository $ip4NetworkRepository;
    protected Generator $faker;
    protected Ip4NetworkUUID $uuid;
    protected ListIP4NetworkByUUIDService $listIPNetwork;
    public function setUp(): void
    {
        $this->ip4NetworkRepository = $this->getMockBuilder(Ip4NetworkRepository::class)->disableOriginalConstructor()->getMock();
        $this->listIPNetwork = new ListIP4NetworkByUUIDService($this->ip4NetworkRepository);
        $this->faker = FakerFactory::create();
        $this->uuid = new Ip4NetworkUUID($this->faker->uuid());
    }

    public function testIPSearchByUUIDNotExist():void
    {
        $this->ip4NetworkRepository->expects($this->any())
            ->method('getByUuid')
            ->willReturn(null);

        $this->expectException(ListIp4NetworkEmptyException::class);
        $this->listIPNetwork->__invoke($this->uuid );
    }
// php bin/phpunit tests/Net/Ip4Network/Application/Services/LisIp4NetworkByUUIDTest.php
    public function testIpSearchByUUIDOK():void
    {
        $ip4NetworkEntity = new Ip4NetworkEntity();
        $ip4NetworkEntity->setName("Mock");
        $ip4NetworkEntity->setUuid($this->uuid->value() );
        $ip4NetworkEntity->setNetmask($this->faker->uuid());
        $ip4NetworkEntity->setGateway($this->faker->uuid());
        $ip4NetworkEntity->setNameServer1($this->faker->ipv4());
        $ip4NetworkEntity->setNameServer2($this->faker->ipv4());
        $ip4NetworkEntity->setNameServer3($this->faker->ipv4());
        $ip4NetworkEntity->setNameServer4($this->faker->ipv4());
        $ip4NetworkEntity->setPriority($this->faker->randomDigit());
        $ip4NetworkEntity->setBroadcast($this->faker->ipv4());

        $this->ip4NetworkRepository->expects($this->any())
            ->method('getByUuid')
            ->willReturn($ip4NetworkEntity);
        $result = $this->listIPNetwork->__invoke($this->uuid );
        $this->assertInstanceOf(Ip4NetworkResponse::class , $result);
    }
}