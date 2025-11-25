<?php
// SonarQube Exclusion for the entire file
// sonar.ignore=true
declare(strict_types=1);

namespace Tests\Net\Ip4Subnet\Application\Service;

use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4FloatGroupEntity;
use GridCP\Net\Ip4Subnet\Domain\Repository\IIp4SubnetOwnerRepository;
use GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Repository\Ip4SubnetRepository;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use GridCP\Net\Ip4Subnet\Application\Response\Ip4SubnetResponse;
use GridCP\Net\Ip4Subnet\Application\Response\Ip4SubnetsResponses;
use GridCP\Net\Ip4Subnet\Application\Service\GetAllSubnetsOfOneClientByUuid;
use GridCP\Net\Ip4Subnet\Domain\Exception\SubnetsNoFound;
use GridCP\Net\Ip4Subnet\Domain\Repository\IIp4SubnetRepository;
use GridCP\Net\Ip4Subnet\Domain\VO\UuidClient;
use GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Entity\Ip4SubnetOwnerEntity;
use GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Entity\Ip4SubnetEntity;

class GetAllSubnetOfOneCLientTest extends TestCase
{
    private IIp4SubnetRepository $subnetRepository;
    private LoggerInterface $logger;
    private GetAllSubnetsOfOneClientByUuid $getAllSubnetsService;

    protected function setUp(): void
    {
        $this->subnetRepository = $this->createMock(\GridCP\Net\Ip4Subnet\Domain\Repository\IIp4SubnetRepository::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->getAllSubnetsService = new GetAllSubnetsOfOneClientByUuid(
            $this->subnetRepository,
            $this->logger
        );
    }

    public function testGetAllSubnetsSuccess(): void
    {
        $uuidClient = new UuidClient('e9a3baa1-1ca4-4c0d-8a0b-cb877491a486');

        $floatGroupEntity = new Ip4FloatGroupEntity();
        $floatGroupEntity->setUuid('e9a3baa1-1ca4-4c0d-8a0b-cb877491B888');

        $subnetEntity = new Ip4SubnetEntity();
        $subnetEntity->setUuid('uuid1');
        $subnetEntity->setIp('192.168.1.0');// NOSONAR
        $subnetEntity->setMask(24);
        $subnetEntity->setFloatgroup($floatGroupEntity->getUuid());

        $this->subnetRepository->expects($this->once())
            ->method('getAllWithRelationsByUuidClient')
            ->with($uuidClient->getValueUuid())
            ->willReturn([$subnetEntity]);

        $this->logger->expects($this->exactly(2))
            ->method('info')
            ->withConsecutive(
                ['Service - Start Get All Subnets Of One Client By Uuid'],
                ['Client found ', ['uuid' => $uuidClient->getValueUuid()]]
            );

        $response = $this->getAllSubnetsService->__invoke($uuidClient);

        $this->assertIsArray($response);
        $this->assertCount(1, $response);
        $this->assertEquals('uuid1', $response[0]->getUuid());
        $this->assertEquals('192.168.1.0', $response[0]->getIp());// NOSONAR
        $this->assertEquals(24, $response[0]->getMask());
    }

    public function testGetAllSubnetsNotFound(): void
    {
        $uuidClient = new UuidClient('e9a3baa1-1ca4-4c0d-8a0b-cb877491a486');
    
        $this->subnetRepository->expects($this->once())
            ->method('getAllWithRelationsByUuidClient')
            ->with($uuidClient->getValueUuid())
            ->willReturn([]);
    
        $this->expectException(SubnetsNoFound::class);
    
        $this->getAllSubnetsService->__invoke($uuidClient);
    }
}
    //     php bin/phpunit tests/Net/Ip4Subnet/Application/Service/GetAllSubnetOfOneCLientTest.php

