<?php
declare(strict_types=1);

namespace Net\Ip4Network\Application\Services;


use Faker\Factory as FakerFactory;
use Faker\Generator;
use GridCP\Common\Domain\ValueObjects\UuidValueObject;
use GridCP\Net\Ip4Network\Domain\VO\Ip4Network;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkActive;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkBroadcast;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkFloatGroup;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkFree;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkGateway;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkName;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkNameServer;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkNetMask;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkNoArp;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkPriority;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkRir;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkSelectableByClient;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkUUID;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4NetworkEntity;
use GridCP\Net\Ip4Network\Infrastructure\DB\MySQL\Repository\Ip4NetworkRepository;
use GridCP\Net\Ip4Network\Application\Services\CreateIPNetwork;
use PHPUnit\Framework\TestCase;

class CreateIp4NetworkTest extends  TestCase
{

    protected Ip4NetworkRepository $ip4NetworkRepository;

    private CreateIPNetwork $createIPNetwork;
    private Generator $faker;

    private Ip4Network $ip4NetworkVO;

    public function setUp(): void
    {
        $this->ip4NetworkRepository = $this->getMockBuilder(Ip4NetworkRepository::class)->disableOriginalConstructor()->getMock();
        $this->createIPNetwork = new CreateIPNetwork($this->ip4NetworkRepository);
        $this->faker = FakerFactory::create();

        $ipNetworkUUID = new Ip4NetworkUUID(UuidValueObject::random()->value());
        $ip4NetworkName = new Ip4NetworkName($this->faker->name());
        $ip4Gateway = new Ip4NetworkGateway($this->faker->ipv4());
        $ipBroadCast = new Ip4NetworkBroadcast($this->faker->ipv4());
        $ipPriority = new Ip4NetworkPriority($this->faker->randomNumber(1));
        $ipNetworkIdFloatGroup = new Ip4NetworkFloatGroup($this->faker->randomNumber(1));
        $ipNetworkNetMask = new Ip4NetworkNetMask($this->faker->ipv4());
        $ipNetworkNameServer = new Ip4NetworkNameServer($this->faker->ipv4());
        $ipNetworkNameServer_2 = new ip4NetworkNameServer($this->faker->ipv4());
        $ipNetworkNameServer_3 = new ip4NetworkNameServer($this->faker->ipv4());
        $ipNetworkNameServer_4 = new ip4NetworkNameServer($this->faker->ipv4());
        $ip4NetworkActive = new Ip4NetworkActive(true);
        $this->ip4NetworkVO = new Ip4Network($ipNetworkUUID, $ip4NetworkName,
            $ipNetworkNameServer, $ipNetworkNameServer_2, $ipNetworkNameServer_3, $ipNetworkNameServer_4, $ipPriority,
            new Ip4NetworkSelectableByClient(false), new Ip4NetworkFree(0),
            $ipNetworkNetMask, $ip4Gateway, $ipBroadCast, new Ip4NetworkNoArp(false), new Ip4NetworkRir(false),
            $ip4NetworkActive);
    }

    public function testCreateNetworkOk():void
    {
        $ip4NetworkEntity = new Ip4NetworkEntity();
        $ip4NetworkEntity->setName("Mock");
        $this->ip4NetworkRepository->expects($this->any())
            ->method('getByName')
            ->willReturn(null);
        $uuid = $this->createIPNetwork->__invoke($this->ip4NetworkVO);
        $this->assertEquals($this->ip4NetworkVO->Uuid()->value(), $uuid);
    }


    public function testErrorCreateNetworkDuplicated(): void
    {
        $ip4NetworkEntity = new Ip4NetworkEntity();
        $ip4NetworkEntity->setName("Mock");
        $this->expectExceptionMessage("Ip4 Network Duplicated ->Mock");
        $this->ip4NetworkRepository->expects($this->any())
            ->method('getByName')
            ->willReturn($ip4NetworkEntity);
        $this->createIPNetwork->__invoke($this->ip4NetworkVO);
        $this->expectExceptionCode(409);
  }

}