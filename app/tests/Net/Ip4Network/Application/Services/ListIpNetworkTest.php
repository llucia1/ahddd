<?php
declare(strict_types=1);

namespace Net\Ip4Network\Application\Services;


use Faker\Factory as FakerFactory;
use Faker\Generator;
use GridCP\Net\Ip4Network\Application\Responses\Ip4NetworksResponse;
use GridCP\Net\Ip4Network\Application\Services\ListIpNetwork;
use GridCP\Net\Ip4Network\Domain\Exception\ListIp4NetworkEmptyException;
use GridCP\Net\Ip4Network\Domain\Model\Ip4NetworkModel;
use GridCP\Net\Ip4Network\Infrastructure\DB\MySQL\Repository\Ip4NetworkRepository;
use PHPUnit\Framework\TestCase;

class ListIpNetworkTest extends TestCase
{
    protected Ip4NetworkRepository $ip4NetworkRepository;
    protected Generator $faker;
    protected ListIpNetwork $listIPNetwork;
    public function setUp(): void
    {
        $this->ip4NetworkRepository = $this->getMockBuilder(Ip4NetworkRepository::class)->disableOriginalConstructor()->getMock();
        $this->listIPNetwork = new ListIpNetwork($this->ip4NetworkRepository);
        $this->faker = FakerFactory::create();
    }

    public function testGetAllOK():void
    {
        $ip4Network = new Ip4NetworkModel();
        $ip4Network->setName($this->faker->name());
        $ip4Network->setUuid($this->faker->uuid());
        $ip4Network->setNameServer1($this->faker->ipv4());
        $ip4Network->setNameServer2( $this->faker->ipv4());
        $ip4Network->setNameServer3($this->faker->ipv4());
        $ip4Network->setNameServer4($this->faker->ipv4());
        $ip4Network->setPriority($this->faker->randomDigit());
        $ip4Network->setGateway($this->faker->ipv4());
        $ip4Network->setNetmask($this->faker->ipv4());

        $this->ip4NetworkRepository->expects($this->any())
            ->method('getAll')
            ->willReturn([$ip4Network]);
        $result = $this->listIPNetwork->__invoke();
        $this->assertInstanceOf(Ip4NetworksResponse::class , $result);
    }
    public function testGetAllNotContent():void
    {
        $this->ip4NetworkRepository->expects($this->any())
            ->method('getAll')
            ->willReturn([]);
        $this->expectException(ListIp4NetworkEmptyException::class);
        $this->listIPNetwork->__invoke();

    }
}