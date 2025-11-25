<?php
// SonarQube Exclusion for the entire file
// sonar.ignore=true
declare(strict_types=1);

namespace Tests\Net\Ip4Subnet\Application\Service;

use GridCP\Common\Domain\Bus\Query\QueryBus;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use GridCP\Net\Ip4Subnet\Application\Response\Ip4SubnetsResponses;
use GridCP\Net\Ip4Subnet\Application\Service\GetAllSubnetsOfOneNetworkByUuid;
use GridCP\Net\Ip4Subnet\Domain\Repository\IIp4SubnetRepository;
use GridCP\Net\Ip4Subnet\Domain\VO\UuidNetwork;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4FloatGroupEntity;
use GridCP\Net\Ip4\Application\Cqrs\Queries\GetAllIp4sOfOneNetworkByUuidQueried;
use GridCP\Net\Ip4\Application\Response\Ip4Response;

use GridCP\Net\Ip4\Application\Response\Ip4sResponse;
use GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Entity\Ip4SubnetEntity;

class GetAllSubnetOfOneNetworkTest extends TestCase
{
    private IIp4SubnetRepository $subnetRepository;
    private QueryBus $queryBus;
    private LoggerInterface $logger;
    private GetAllSubnetsOfOneNetworkByUuid $getAllSubnetsService;
    private UuidNetwork $uuidNetwork;

    private const UUID_NETWORK = 'e9a3baa1-1ca4-4c0d-8a0b-cb877491a486';
    private const UUID_IP4 = 'uuid-ip4';
    private const UUID_SUBNET = 'uuid-subnet';
    private const IP_ADDRESS = '192.168.1.1';// NOSONAR
    private const SUBNET_IP = '192.168.1.0';// NOSONAR
    private const MASK = 24;
    private const FLOAT_GROUP_UUID = 'e9a3baa1-1ca4-4c0d-8a0b-cb877491B888';

    protected function setUp(): void
    {
        $this->subnetRepository = $this->createMock(IIp4SubnetRepository::class);
        $this->queryBus = $this->createMock(QueryBus::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->getAllSubnetsService = new GetAllSubnetsOfOneNetworkByUuid(
            $this->subnetRepository,
            $this->queryBus,
            $this->logger
        );
        $this->uuidNetwork = new UuidNetwork(self::UUID_NETWORK);
    }

    public function testGetAllSubnetsSuccess(): void
    {
        $ip4Response = $this->createIp4Response();
        $subnetEntity = $this->createSubnetEntity();

        $this->queryBus->expects($this->once())
            ->method('ask')
            ->with($this->isInstanceOf(GetAllIp4sOfOneNetworkByUuidQueried::class))
            ->willReturn(new Ip4sResponse($ip4Response));

        $this->subnetRepository->expects($this->once())
            ->method('findSubnetContainingIp')
            ->with(self::IP_ADDRESS)
            ->willReturn($subnetEntity);

        $this->logger->expects($this->exactly(2))
            ->method('info')
            ->withConsecutive(
                ['Service - Start Get All Subnets Of One Network By Uuid'],
                ['Check Ip has Subnet: ' . self::IP_ADDRESS]
            );

        $response = $this->getAllSubnetsService->__invoke($this->uuidNetwork);

        $this->assertInstanceOf(Ip4SubnetsResponses::class, $response);
        $this->assertCount(1, $response->gets());
    }

    public function testNoSubnetsFound(): void
    {
        $ip4Response = $this->createIp4Response();

        $this->queryBus->expects($this->once())
            ->method('ask')
            ->willReturn(new Ip4sResponse($ip4Response));

        $this->subnetRepository->expects($this->once())
            ->method('findSubnetContainingIp')
            ->with(self::IP_ADDRESS)
            ->willReturn(null);

        $this->logger->expects($this->exactly(2))
            ->method('info')
            ->withConsecutive(
                ['Service - Start Get All Subnets Of One Network By Uuid'],
                ['Check Ip has Subnet: ' . self::IP_ADDRESS]
            );

        $response = $this->getAllSubnetsService->__invoke($this->uuidNetwork);

        $this->assertInstanceOf(Ip4SubnetsResponses::class, $response);
        $this->assertCount(0, $response->gets());
    }

    private function createIp4Response(): Ip4Response
    {
        return new Ip4Response(
            self::UUID_IP4,
            self::IP_ADDRESS,
            null,
            true
        );
    }

    private function createSubnetEntity(): Ip4SubnetEntity
    {
        $floatGroupEntity = new Ip4FloatGroupEntity();
        $floatGroupEntity->setUuid(self::FLOAT_GROUP_UUID);

        $subnetEntity = new Ip4SubnetEntity();
        $subnetEntity->setUuid(self::UUID_SUBNET);
        $subnetEntity->setIp(self::SUBNET_IP);
        $subnetEntity->setMask(self::MASK);
        $subnetEntity->setFloatgroup($floatGroupEntity->getUuid());

        return $subnetEntity;
    }
    //     php bin/phpunit tests/Net/Ip4Subnet/Application/Service/GetAllSubnetOfOneNetworkTest.php
}
