<?php
declare(strict_types=1);

namespace Node\Application\Service;

use Faker\Factory as FakerFactory;
use Faker\Generator;
use GridCP\Common\Domain\Bus\Query\QueryBus;
use GridCP\Node\Application\Service\DeleteNodeService;
use GridCP\Node\Application\Service\GetAllFreeIpsOfOneNodeService;
use GridCP\Node\Domain\Exception\FreeIpsNotExistError;
use GridCP\Node\Domain\Exception\ListNodesEmptyException;
use GridCP\Node\Domain\Repository\INodeRepository;
use GridCP\Node\Domain\VO\ClientUuid;
use GridCP\Node\Domain\VO\NodeUuid;
use GridCP\Node\Infrastructure\DB\MySQL\Entity\NodeEntity;
use GridCP\Node\Infrastructure\DB\MySQL\Repository\NodeRepository;
use GridCP\Proxmox\Vm\Application\Cqrs\Queries\GetFreeIpsOfNodeByUuidQueried;
use GridCP\Proxmox\Vm\Application\Response\FreeIpsNodeQueryResponse;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use stdClass;

class GetAllFreeIpsOfOneNodeServiceTest extends TestCase
{
    private MockObject $nodeRepository;
    private MockObject $logger;
    private MockObject $queryBus;
    private GetAllFreeIpsOfOneNodeService $service;
    private \Faker\Generator $faker;

    protected function setUp(): void
    {
        $this->faker = FakerFactory::create();
        $this->nodeRepository = $this->createMock(INodeRepository::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->queryBus = $this->createMock(QueryBus::class);

        $this->service = new GetAllFreeIpsOfOneNodeService(
            $this->nodeRepository,
            $this->logger,
            $this->queryBus
        );
    }

    public function testShouldReturnFreeIps(): void
    {
        $uuid = new NodeUuid($this->faker->uuid());
        $clientUuid = new ClientUuid($this->faker->uuid());
        $gcpName = 'test-gcp';

        $node = $this->createMock(NodeEntity::class);
        $node->method('getGcpName')->willReturn($gcpName);

        $this->nodeRepository
            ->expects($this->once())
            ->method('findByUuid')
            ->with($uuid->value())
            ->willReturn($node);


        $ipsResponse = new FreeIpsNodeQueryResponse ([
                [ 
                    "uuid" => "3be6df11-321e-42f4-a229-3a01d48ccd82",
                    "ip" => "192.168.1.1",
                    "network" => [ 
                        "uuid" => "b863ca85-8d0a-43b9-b119-cc7a299f1289",
                        "name" => "Ubrique1"
                    ],
                    "priority"=> 8
                ],
                [ 
                    "uuid" => "dc8c4d6d-794e-4cdb-b7a7-44d090a093a2",
                    "ip" => "192.168.1.2",
                    "network" => [ 
                        "uuid" => "b863ca85-8d0a-43b9-b119-cc7a299f1289",
                        "name" => "Ubrique1"
                    ],
                    "priority"=> 8
                ]

        ]);

        $this->queryBus
            ->expects($this->once())
            ->method('ask')
            ->with(new GetFreeIpsOfNodeByUuidQueried($gcpName, $clientUuid->value()) )
            ->willReturn($ipsResponse);

        $result = $this->service->__invoke($uuid, $clientUuid);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals('192.168.1.1', $result[0]['ip']);
    }

    public function testShouldThrowExceptionIfNodeNotFound(): void
    {
        $uuid = new NodeUuid($this->faker->uuid());

        $this->nodeRepository
            ->expects($this->once())
            ->method('findByUuid')
            ->with($uuid->value())
            ->willReturn(null);

        $this->expectException(ListNodesEmptyException::class);

        $this->service->__invoke($uuid, null);
    }

    public function testShouldThrowExceptionIfIpsAreNull(): void
    {
        $uuid = new NodeUuid($this->faker->uuid());
        $gcpName = 'test-gcp';

        $node = $this->createMock(NodeEntity::class);
        $node->method('getGcpName')->willReturn($gcpName);

        $this->nodeRepository
            ->method('findByUuid')
            ->willReturn($node);

        $this->queryBus
            ->method('ask')
            ->willReturn(null);

        $this->expectException(FreeIpsNotExistError::class);

        $this->service->__invoke($uuid, null);
    }
}
    //     php bin/phpunit tests/Node/Application/Service/GetAllFreeIpsOfOneNodeServiceTest.php