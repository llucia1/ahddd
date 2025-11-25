<?php
// SonarQube Exclusion for the entire file
// sonar.ignore=true
declare(strict_types=1);

namespace Tests\Net\Ip4Subnet\Application\Service;

use GridCP\Common\Domain\Bus\Query\QueryBus;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use GridCP\Net\Ip4Subnet\Domain\Repository\IIp4SubnetRepository;
use GridCP\Net\Ip4Subnet\Domain\VO\UuidNetwork;
use GridCP\Net\Ip4\Application\Cqrs\Queries\GetAllIp4sOfOneNetworkByUuidQueried;
use GridCP\Net\Ip4\Application\Response\GetAllIpsOfOneNetworkExceptionResponse;
use GridCP\Net\Ip4\Application\Response\Ip4Response;

use GridCP\Net\Ip4\Application\Response\Ip4sResponse;
use GridCP\Net\Ip4\Domain\Exceptions\NerworkNoExistException;
use GridCP\Net\Ip4Subnet\Application\Response\Ip4SubnetAvaibleResponse;
use GridCP\Net\Ip4Subnet\Application\Service\GetAllFreeSubnetsOfOneNetworkByUuid;
use GridCP\Net\Ip4Subnet\Domain\Exception\NetworknotExistException;
use GridCP\Net\Ip4Subnet\Domain\Exception\SubnetsNoFound;
use GridCP\Net\Ip4Subnet\Domain\VO\SubnetMask;

class GetAllFreeSubnetsOfOneNetworkByUuidTest extends TestCase
{

    private IIp4SubnetRepository $subnetRepository;
    private QueryBus $queryBus;
    private LoggerInterface $logger;
    private GetAllFreeSubnetsOfOneNetworkByUuid $service;

    protected function setUp(): void
    {
        $this->subnetRepository = $this->createMock(IIp4SubnetRepository::class);
        $this->queryBus = $this->createMock(QueryBus::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->service = new GetAllFreeSubnetsOfOneNetworkByUuid(
            $this->subnetRepository,
            $this->queryBus,
            $this->logger
        );
    }

    public function testGetAllFreeSubnetsSuccess(): void
    {
        $uuidNetwork = new UuidNetwork('123e4567-e89b-12d3-a456-426614174000');// NOSONAR
        $mask = new SubnetMask(24);

        $mockIps = [
            new Ip4Response('uuid1', '192.168.0.1', null, true),
            new Ip4Response('uuid2', '192.168.0.2', null, true),
        ];

        $this->queryBus->expects($this->once())
            ->method('ask')
            ->with($this->isInstanceOf(GetAllIp4sOfOneNetworkByUuidQueried::class))
            ->willReturn(new Ip4sResponse(...$mockIps));

        $this->subnetRepository->expects($this->exactly(count($mockIps)))
            ->method('findSubnetContainingIp')
            ->withConsecutive(['192.168.0.1'], ['192.168.0.2'])// NOSONAR
            ->willReturnOnConsecutiveCalls(null, null);

        $this->logger->expects($this->exactly(3))
            ->method('info')
            ->withConsecutive(
                ['Service - Start Get All Subnets Of One Network By Uuid'],
                ['Processing IP: 192.168.0.1'],// NOSONAR
                ['Processing IP: 192.168.0.2']// NOSONAR
            );

        $response = $this->service->__invoke($uuidNetwork, $mask);

        $this->assertCount(2, $response);
        foreach ($response as $index => $subnet) {
            $this->assertInstanceOf(Ip4SubnetAvaibleResponse::class, $subnet);
            $this->assertEquals($mockIps[$index]->ip(), $subnet->ip());
            $this->assertEquals(24, $subnet->mask());
        }
    }

    public function testGetAllFreeSubnetsNoIpsFound(): void
    {
        $uuidNetwork = new UuidNetwork('123e4567-e89b-12d3-a456-426614174000');// NOSONAR
        $mask = new SubnetMask(24);

        $this->queryBus->expects($this->once())
            ->method('ask')
            ->with($this->isInstanceOf(GetAllIp4sOfOneNetworkByUuidQueried::class))
            ->willReturn(new Ip4sResponse());

        $this->logger->expects($this->once())
            ->method('error')
            ->with($this->stringContains('No subnets found for mask'));

        $this->expectException(SubnetsNoFound::class);

        $this->service->__invoke($uuidNetwork, $mask);
    }

    public function testGetAllFreeSubnetsExceptionHandling(): void
    {
        $uuidNetwork = new UuidNetwork('123e4567-e89b-12d3-a456-426614174000');// NOSONAR
        $mask = new SubnetMask(24);

        $exception = new NerworkNoExistException();

        $this->queryBus->expects($this->once())
            ->method('ask')
            ->with($this->isInstanceOf(GetAllIp4sOfOneNetworkByUuidQueried::class))
            ->willReturn(new GetAllIpsOfOneNetworkExceptionResponse($exception));

        $this->logger->expects($this->once())
            ->method('error')
            ->with($this->stringContains('Error retrieving IPs for network'));

        $this->expectException(NetworknotExistException::class);

        $this->service->__invoke($uuidNetwork, $mask);
    }


    //     php bin/phpunit tests/Net/Ip4Subnet/Application/Service/GetAllSubnetOfOneNetworkTest.php
}
