<?php
declare(strict_types=1);

namespace Net\Ip4Network\Application\Services;

use Faker\Factory as FakerFactory;
use Faker\Generator;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4NetworkEntity;
use GridCP\Net\Ip4Network\Application\Services\PatchIPNetworkService;
use GridCP\Net\Ip4Network\Domain\Exception\NetworkNotExistException;
use GridCP\Net\Ip4Network\Domain\Repository\IIp4NetworkRepository;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkActive;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkBroadcast;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkFree;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkGateway;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkName;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkNameServer;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkNetMask;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkNoArp;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkPatch;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkPriority;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkRir;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkSelectableByClient;
use GridCP\Net\Ip4Network\Domain\VO\Ip4NetworkUUID;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class PatchIpNetworkTest extends TestCase
{
    protected IIp4NetworkRepository $repository;
    protected LoggerInterface $logger;

    protected PatchIPNetworkService $patchIpNetwork;

    public function setUp(): void
    {
        $this->repository = $this->getMockBuilder(IIp4NetworkRepository::class)->disableOriginalConstructor()->getMock();
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->patchIpNetwork = new PatchIPNetworkService($this->repository, $this->logger);
    }

    public function testVmUpdateOK():void
    {
        $faker = FakerFactory::create();
        $uuid = $faker->uuid();
        $networkEntity = new Ip4NetworkEntity();

        $networkEntity->setUuid( $uuid );
        $networkEntity->setName( $faker->name() );
        $networkEntity->setNameServer1( $faker->ipv4() );
        $networkEntity->setNameServer2( $faker->ipv4() );
        $networkEntity->setNameServer3( $faker->ipv4() );
        $networkEntity->setNameServer4( $faker->ipv4() );
        $networkEntity->setPriority($faker->randomNumber(2));
        $networkEntity->setSelectableByClient(true);
        $networkEntity->setFree($faker->randomNumber(2));
        $networkEntity->setNetmask($faker->ipv4());
        $networkEntity->setGateway($faker->ipv4());
        $networkEntity->setBroadcast($faker->ipv4());
        $networkEntity->setNoArp(true);
        $networkEntity->setRir(true);
        $networkEntity->setActive(true);


        $ipNetworkUUID = new Ip4NetworkUUID($uuid);
        $ip4NetworkName = new Ip4NetworkName($faker->name());
        $ip4Gateway = new Ip4NetworkGateway($faker->ipv4());
        $ipBroadCast = new Ip4NetworkBroadcast($faker->ipv4());
        $ipPriority = new Ip4NetworkPriority(1);
        $ipFree = new Ip4NetworkFree(0);
        $ipNetworkNetMask = new Ip4NetworkNetMask($faker->ipv4());
        $ipNetworkNameServer = new Ip4NetworkNameServer($faker->ipv4());
        $ipNetworkNameServer_2 = new Ip4NetworkNameServer($faker->ipv4());
        $ipNetworkNameServer_3 = new Ip4NetworkNameServer($faker->ipv4());
        $ipNetworkNameServer_4 = new Ip4NetworkNameServer($faker->ipv4());
        $ip4NetworkActive = new Ip4NetworkActive(true);
        $ip4NetworkNoArp = new Ip4NetworkNoArp(false);
        $ip4NetworkRir = new Ip4NetworkRir(true);
        $ip4NetworkNoSelectableByClient = new Ip4NetworkSelectableByClient(false);


        
        $ip4Network = new Ip4NetworkPatch(
            $ipNetworkUUID, 
            $ip4NetworkName,
            $ipNetworkNameServer, 
            $ipNetworkNameServer_2, 
            $ipNetworkNameServer_3, 
            $ipNetworkNameServer_4, 
            $ipPriority,
            $ip4NetworkNoSelectableByClient, 
            $ipFree,
            $ipNetworkNetMask, 
            $ip4Gateway, 
            $ipBroadCast, 
            $ip4NetworkNoArp, 
            $ip4NetworkRir,
            $ip4NetworkActive
        );    


        $this->repository->expects($this->any())
            ->method('getByUuid')
            ->with($uuid)
            ->willReturn($networkEntity);

        $this->patchIpNetwork->__invoke($ip4Network, $uuid);
        $this->expectNotToPerformAssertions();
    }

    
    public function testUpdateOnlyAnyParametersOK():void
    {
        $faker = FakerFactory::create();
        $uuid = $faker->uuid();
        $networkEntity = new Ip4NetworkEntity();

        $networkEntity->setUuid( $uuid );
        $networkEntity->setName( $faker->name() );
        $networkEntity->setNameServer1( $faker->ipv4() );
        $networkEntity->setNameServer2( $faker->ipv4() );
        $networkEntity->setNameServer3( $faker->ipv4() );
        $networkEntity->setNameServer4( $faker->ipv4() );
        $networkEntity->setPriority($faker->randomNumber(2));
        $networkEntity->setSelectableByClient(true);
        $networkEntity->setFree($faker->randomNumber(2));
        $networkEntity->setNetmask($faker->ipv4());
        $networkEntity->setGateway($faker->ipv4());
        $networkEntity->setBroadcast($faker->ipv4());
        $networkEntity->setNoArp(true);
        $networkEntity->setRir(true);
        $networkEntity->setActive(true);


        $ipNetworkUUID = new Ip4NetworkUUID($uuid);
        $ipBroadCast = new Ip4NetworkBroadcast($faker->ipv4());
        $ipFree = new Ip4NetworkFree(0);
        $ipNetworkNameServer = new Ip4NetworkNameServer($faker->ipv4());

        
        $ip4Network = new Ip4NetworkPatch(
            $ipNetworkUUID, 
            null,
            $ipNetworkNameServer, 
            null, 
            null, 
            null, 
            null,
            null, 
            $ipFree,
            null, 
            null, 
            $ipBroadCast, 
            null, 
            null,
            null
        ); 

        $this->repository->expects($this->any())
            ->method('getByUuid')
            ->with($uuid)
            ->willReturn($networkEntity);

        $this->patchIpNetwork->__invoke($ip4Network, $uuid);
        $this->expectNotToPerformAssertions();
    }

    public function testByUUIDNotExist():void
    {
        $faker = FakerFactory::create();

        $uuid = $faker->uuid();
        $ipNetworkUUID = new Ip4NetworkUUID($uuid);
        $ipBroadCast = new Ip4NetworkBroadcast($faker->ipv4());
        $ipFree = new Ip4NetworkFree(0);
        $ipNetworkNameServer = new Ip4NetworkNameServer($faker->ipv4());

        
        $ip4Network = new Ip4NetworkPatch(
            $ipNetworkUUID, 
            null,
            $ipNetworkNameServer, 
            null, 
            null, 
            null, 
            null,
            null, 
            $ipFree,
            null, 
            null, 
            $ipBroadCast, 
            null, 
            null,
            null
        );       


        $this->repository->expects($this->any())
                           ->method('getByUuid')
                           ->with($uuid)
                           ->willReturn(null);

        $this->expectException(NetworkNotExistException::class);
        $this->patchIpNetwork->__invoke($ip4Network, $uuid);
    } 
    // php bin/phpunit tests/Net/Ip4Network/Application/Services/PatchIpNetworkTest.php
}