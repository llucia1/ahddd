<?php
// SonarQube Exclusion for the entire file
// sonar.ignore=true
declare(strict_types=1);

namespace Tests\Net\Ip4Subnet\Application\Service;

use GridCP\Net\Ip4Subnet\Domain\Repository\IIp4SubnetOwnerRepository;
use GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Repository\Ip4SubnetRepository;
use PHPUnit\Framework\TestCase;
use GridCP\Common\Domain\Bus\Query\QueryBus;
use Faker\Factory as FakerFactory;
use GridCP\Client\Application\Response\ClientEntityResponse;
use GridCP\Client\Infrastructure\DB\MySQL\Entity\ClientEntity;
use GridCP\Net\Ip4Subnet\Application\Service\AddPropertySubnet;
use Psr\Log\LoggerInterface;
use GridCP\Net\Ip4Subnet\Domain\Exception\SubnetDuplicated;
use GridCP\Net\Ip4Subnet\Domain\Exception\SubnetNoFound;
use GridCP\Net\Ip4Subnet\Domain\Exception\ClientNoFound;
use GridCP\Net\Ip4Subnet\Domain\VO\SubnetUuid;
use GridCP\Net\Ip4Subnet\Domain\VO\UuidClient;
use GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Entity\Ip4SubnetEntity;
use GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Entity\Ip4SubnetOwnerEntity;

class AddPropertySubnetTest extends TestCase
{
    private Ip4SubnetRepository $ip4SubnetRepository;
    private IIp4SubnetOwnerRepository $propertySubnetRepository;
    private LoggerInterface $logger;
    private QueryBus $queryBus;
    private AddPropertySubnet $propertySubnetService;

    private SubnetUuid $subnetUuid;
    private UuidClient $clientUuid;
    private Ip4SubnetEntity $subnetEntity;

    protected function setUp(): void
    {
        $faker = FakerFactory::create();

        $this->subnetUuid = new SubnetUuid($faker->uuid());
        $this->clientUuid = new UuidClient($faker->uuid());

        $this->ip4SubnetRepository = $this->createMock(Ip4SubnetRepository::class);
        $this->propertySubnetRepository = $this->createMock(IIp4SubnetOwnerRepository::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->queryBus = $this->createMock(QueryBus::class);

        $this->propertySubnetService = new AddPropertySubnet(
            $this->ip4SubnetRepository,
            $this->propertySubnetRepository,
            $this->logger,
            $this->queryBus
        );

        $this->subnetEntity = $this->createMock(Ip4SubnetEntity::class);
        $this->subnetEntity->method('getUuid')->willReturn($this->subnetUuid->value());
    }

    public function testAddPropertySubnetSuccess(): void
    {
        $mockClientEntity = $this->createMock(ClientEntity::class); // Crear un mock de ClientEntity
        $mockClientResponse = $this->createMock(ClientEntityResponse::class);
        $mockClientResponse->method('get')->willReturn($mockClientEntity); // Configurar el método get()
    
        $this->queryBus->expects($this->once())
            ->method('ask')
            ->willReturn($mockClientResponse);
    
        $this->ip4SubnetRepository->expects($this->once())
            ->method('findByUuid')
            ->with($this->subnetUuid->value())
            ->willReturn($this->subnetEntity);
    
        $this->propertySubnetRepository->expects($this->once())
            ->method('findBySubnetUuid')
            ->with($this->subnetUuid->value())
            ->willReturn(null);
    
        $this->propertySubnetRepository->expects($this->once())
            ->method('save')
            ->willReturnCallback(function ($propertySubnet) {
                $this->assertInstanceOf(Ip4SubnetOwnerEntity::class, $propertySubnet);
                $this->assertSame($this->subnetUuid->value(), $propertySubnet->getSubnet()->getUuid());
                $this->assertSame($this->clientUuid->getValueUuid(), $propertySubnet->getClientUuid());
            });
    
        $uuid = $this->propertySubnetService->__invoke($this->subnetUuid, $this->clientUuid);
    
        $this->assertIsString($uuid);
    }
    

    public function testAddPropertySubnetNotFound(): void
    {
        $mockClientEntity = $this->createMock(ClientEntity::class); // Cliente válido
        $mockClientResponse = $this->createMock(ClientEntityResponse::class);
        $mockClientResponse->method('get')->willReturn($mockClientEntity);

        $this->queryBus->expects($this->once())
            ->method('ask')
            ->willReturn($mockClientResponse);

        $this->ip4SubnetRepository->expects($this->once())
            ->method('findByUuid')
            ->with($this->subnetUuid->value())
            ->willReturn(null);

        $this->expectException(SubnetNoFound::class);

        $this->propertySubnetService->__invoke($this->subnetUuid, $this->clientUuid);
    }

    public function testAddPropertySubnetDuplicated(): void
    {
        $mockClientEntity = $this->createMock(ClientEntity::class); // Cliente válido
        $mockClientResponse = $this->createMock(ClientEntityResponse::class);
        $mockClientResponse->method('get')->willReturn($mockClientEntity);

        $this->queryBus->expects($this->once())
            ->method('ask')
            ->willReturn($mockClientResponse);

        $this->ip4SubnetRepository->expects($this->once())
            ->method('findByUuid')
            ->with($this->subnetUuid->value())
            ->willReturn($this->subnetEntity);

        $this->propertySubnetRepository->expects($this->once())
            ->method('findBySubnetUuid')
            ->with($this->subnetUuid->value())
            ->willReturn($this->createMock(Ip4SubnetOwnerEntity::class));

        $this->expectException(SubnetDuplicated::class);

        $this->propertySubnetService->__invoke($this->subnetUuid, $this->clientUuid);
    }

    public function testAddPropertySubnetClientNotFound(): void
    {
        $mockClientResponse = $this->createMock(ClientEntityResponse::class);
        $mockClientResponse->method('get')->willReturn(null); // Simular que no se encuentra el cliente
    
        $this->queryBus->expects($this->once())
            ->method('ask')
            ->willReturn($mockClientResponse);
    
        $this->expectException(ClientNoFound::class);
    
        $this->propertySubnetService->__invoke($this->subnetUuid, $this->clientUuid);
    }

    //     php bin/phpunit tests/Net/Ip4Subnet/Application/Service/AddPropertySubnetTest.php
}



