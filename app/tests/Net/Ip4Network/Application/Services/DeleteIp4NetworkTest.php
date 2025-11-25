<?php
declare(strict_types=1);

namespace Net\Ip4Network\Application\Services;

use Faker\Factory as FakerFactory;
use Faker\Generator;
use GridCP\Net\Ip4Network\Domain\Exception\ListIp4NetworkEmptyException;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4NetworkEntity;
use GridCP\Net\Ip4Network\Infrastructure\DB\MySQL\Repository\Ip4NetworkRepository;
use GridCP\Net\Ip4Network\Application\Services\DeleteIPNetworkService;
use GridCP\Net\Ip4Network\Domain\Exception\HasIp4sNetworkException;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkUUID;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class DeleteIp4NetworkTest extends TestCase
{
    protected Ip4NetworkRepository $ip4NetworkRepository;
    protected Generator $faker;
    protected DeleteIPNetworkService $deleteIPNetwork;
    protected Ip4NetworkEntity $IpNetworkEntity;
    protected Ip4NetworkUUID $uuid;
    public function setUp(): void
    {
        $this->ip4NetworkRepository = $this->getMockBuilder(Ip4NetworkRepository::class)->disableOriginalConstructor()->getMock();
        $this->deleteIPNetwork = new DeleteIPNetworkService($this->ip4NetworkRepository);
        $this->faker = FakerFactory::create();


        $this->IpNetworkEntity = new Ip4NetworkEntity();
        $this->IpNetworkEntity->setName("Mock");
        $this->IpNetworkEntity->setUuid($this->faker->uuid());


        $this->uuid = new Ip4NetworkUUID($this->IpNetworkEntity->getUuid());
    }

    public function testDeletedNetworkOk():void
    {

        $this->ip4NetworkRepository->expects($this->any())
            ->method('getByUuid')
            ->with($this->uuid->value())
            ->willReturn($this->IpNetworkEntity);

        $this->ip4NetworkRepository->expects($this->any())
            ->method('findIpsByNetworkUuid')
            ->with($this->uuid->value())
            ->willReturn([]);

        $this->ip4NetworkRepository->expects($this->any())
            ->method('delete')
            ->with($this->uuid->value());

        $result = $this->deleteIPNetwork->__invoke($this->uuid);
        $this->assertEquals($this->uuid->value(), $result);


    }
    public function testDeletedNetworkNotFound():void
    {

        $this->ip4NetworkRepository->expects($this->any())
            ->method('getByUuid')
            ->with($this->uuid->value())
            ->willReturn(null);

        $this->expectException(ListIp4NetworkEmptyException::class);


        $this->deleteIPNetwork->__invoke($this->uuid);


    }

    public function testDeletedNetworkWithAssociatedIp4s():void
    {

        $this->ip4NetworkRepository->expects($this->any())
            ->method('getByUuid')
            ->with($this->uuid->value())
            ->willReturn($this->IpNetworkEntity);

        $this->ip4NetworkRepository->expects($this->any())
            ->method('findIpsByNetworkUuid')
            ->with($this->uuid->value())
            ->willReturn([$this->IpNetworkEntity]);

        $this->expectException(HasIp4sNetworkException::class);
    

        $this->deleteIPNetwork->__invoke($this->uuid);


    }

    
}// php bin/phpunit tests/Net/Ip4Network/Application/Services/DeleteIp4NetworkTest.php