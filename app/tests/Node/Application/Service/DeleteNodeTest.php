<?php
declare(strict_types=1);

namespace Node\Application\Service;

use Faker\Factory as FakerFactory;
use Faker\Generator;
use GridCP\Node\Application\Service\DeleteNodeService;
use GridCP\Node\Domain\Exception\ListNodesEmptyException;
use GridCP\Node\Infrastructure\DB\MySQL\Entity\NodeEntity;
use GridCP\Node\Infrastructure\DB\MySQL\Repository\NodeRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class DeleteNodeTest extends TestCase
{
    protected NodeRepository $nodeRepository;
    protected Generator $faker;
    protected DeleteNodeService $deleteNodeService;

    public function setUp(): void
    {
        $this->nodeRepository = $this->getMockBuilder(NodeRepository::class)->disableOriginalConstructor()->getMock();
        $this->deleteNodeService = new DeleteNodeService($this->nodeRepository);
        $this->faker = FakerFactory::create();
    }

    public function testDeletedNodeOk():void
    {
        $nodeEntity = new NodeEntity();
        $nodeEntity->setGpcName("Mock");
        $nodeEntity->setUuid($this->faker->uuid());
        $this->nodeRepository->expects($this->any())
            ->method('findByUuid')
            ->willReturn($nodeEntity);
        $result = $this->deleteNodeService->delete($nodeEntity->getUuid());
        $this->assertInstanceOf(JsonResponse::class , $result);
        $this->assertArrayHasKey("uuid", json_decode($result->getContent(),true));
    }

    public function testDeletedNodeNotExistFail():void
    {
        $nodeEntity = new NodeEntity();
        $nodeEntity->setGpcName("Mock");
        $nodeEntity->setUuid($this->faker->uuid());
        $this->nodeRepository->expects($this->any())
            ->method('findByUuid')
            ->willReturn(null);

        $this->expectException(ListNodesEmptyException::class);
        $this->deleteNodeService->delete($nodeEntity->getUuid());

    }

}