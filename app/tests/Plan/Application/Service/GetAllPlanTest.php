<?php
declare(strict_types=1);
namespace Tests\Plan\Application\Service;

use Faker\Generator;
use GridCP\Plan\Application\Response\PlanResponse;
use GridCP\Plan\Application\Response\PlanResponses;
use GridCP\Plan\Application\Service\GetAllPlanService;
use GridCP\Plan\Domain\Exception\ListPlansEmptyException;
use GridCP\Plan\Infrastructure\DB\MySQL\Repository\PlanRepository;
use PHPUnit\Framework\TestCase;
use GridCP\Plan\Infrastructure\DB\MySQL\Entity\PlanEntity;

use Psr\Log\LoggerInterface;
use Tests\Plan\Application\Helper\PlanTools;

class GetAllPlanTest extends TestCase
{
    use PlanTools;
    protected PlanRepository $planRepository;
    protected Generator $faker;
    private GetAllPlanService $planService;

    private array $plans;

    public function setUp(): void
    {
        $loggerMock = $this->createMock(LoggerInterface::class);

        $this->planRepository = $this->getMockBuilder( PlanRepository::class )->disableOriginalConstructor()->getMock();
        $this->planService = new GetAllPlanService($this->planRepository, $loggerMock);

        $plan1 = $this->createPlan();
        $plan2 = $this->createPlan();
        $planEntity1 = $this->createPlanEntity($plan1);
        $planEntity2 = $this->createPlanEntity($plan2);

        $this->plans = [$planEntity1, $planEntity2];
    }

    public function testGetAllPlansIsEmpty():void
    {
        $this->planRepository->expects($this->any())
            ->method('getAll')
            ->willReturn([]);

        $this->expectException(ListPlansEmptyException::class);
        $this->planService->__invoke();
    }
    //     php bin/phpunit tests/Plan/Application/Service/GetAllPlanTest.php

    public function testPlanSearchByUUIDOK():void
    {
        $this->planRepository->expects($this->any())
            ->method('getAll')
            ->willReturn($this->plans);

        $result = $this->planService->__invoke();
        $this->assertInstanceOf(PlanResponses::class , $result);

        $resultDatas = $result->gets();
        $this->assertContainsOnlyInstancesOf(PlanResponse::class , $resultDatas);
        $this->assertCount(2, $resultDatas);
    }
}
