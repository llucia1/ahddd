<?php
// SonarQube Exclusion for the entire file
// sonar.ignore=true
declare(strict_types=1);

namespace Tests\Net\Ip4Subnet\Application\Service;

use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4FloatGroupEntity;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use GridCP\Net\Ip4Subnet\Application\Response\Ip4SubnetsResponses;
use GridCP\Net\Ip4Subnet\Application\Service\GetAllSubnets;
use GridCP\Net\Ip4Subnet\Domain\Exception\SubnetsNoFound;
use GridCP\Net\Ip4Subnet\Domain\Repository\IIp4SubnetRepository;
use GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Entity\Ip4SubnetOwnerEntity;
use GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Entity\Ip4SubnetEntity;

class GetAllSubnetTest extends TestCase
{
    private IIp4SubnetRepository $subnetRepository;
    private LoggerInterface $logger;
    private GetAllSubnets $getAllSubnetsService;

    protected function setUp(): void
    {
        $this->subnetRepository = $this->createMock(IIp4SubnetRepository::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->getAllSubnetsService = new GetAllSubnets(
            $this->subnetRepository,
            $this->logger
        );
    }

    public function testGetAllSubnetsSuccess(): void
    {
        $subnetEntity = $this->createConfiguredMock(Ip4SubnetEntity::class, [
            'getUuid' => 'uuid1',
            'getIp' => '192.168.1.0',// NOSONAR
            'getMask' => 24,
            'getUuidFloatgroup' => 'e9a3baa1-1ca4-4c0d-8a0b-cb877491B888',
        ]);

        $this->subnetRepository->expects($this->once())
            ->method('getAllWithRelation')
            ->willReturn([$subnetEntity]);

        $this->logger->expects($this->once())
            ->method('info')
            ->with('Service - Start Get All Subnets');

        $response = $this->getAllSubnetsService->__invoke();

        $this->assertIsArray($response);
        $this->assertCount(1, $response);

        $subnetResponse = $response[0];
        $this->assertEquals('uuid1', $subnetResponse->getUuid());
        $this->assertEquals('192.168.1.0', $subnetResponse->getIp());// NOSONAR
        $this->assertEquals(24, $subnetResponse->getMask());
        $this->assertEquals('e9a3baa1-1ca4-4c0d-8a0b-cb877491B888', $subnetResponse->getUuidFloatgroup());
    }

    public function testGetAllSubnetsNotFound(): void
    {
        $this->subnetRepository->expects($this->once())
            ->method('getAllWithRelation')
            ->willReturn([]);

        $this->logger->expects($this->once())
            ->method('info')
            ->with('Service - Start Get All Subnets');

        $this->expectException(SubnetsNoFound::class);

        $this->getAllSubnetsService->__invoke();
    }
    // php bin/phpunit tests/Net/Ip4Subnet/Application/Service/GetAllSubnetTest.php

}