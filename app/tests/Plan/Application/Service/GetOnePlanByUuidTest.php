<?php
declare(strict_types=1);
namespace Tests\Plan\Application\Service;

use Faker\Generator;
use GridCP\Net\Ip4\Application\Response\Ip4Response;
use GridCP\Net\Ip4\Domain\Exceptions\ListIp4EmptyException;
use GridCP\Plan\Application\Response\PlanResponse;
use GridCP\Plan\Domain\Exception\PlanNotExistException;
use GridCP\Plan\Infrastructure\DB\MySQL\Repository\PlanRepository;
use PHPUnit\Framework\TestCase;
use GridCP\Plan\Application\Service\GetPlanByUuidService;
use GridCP\Plan\Domain\VO\Plan;
use GridCP\Plan\Infrastructure\DB\MySQL\Entity\PlanEntity;

use Psr\Log\LoggerInterface;
use Tests\Plan\Application\Helper\PlanTools;

class GetOnePlanByUuidTest extends TestCase
{
    use PlanTools;
    protected PlanRepository $planRepository;
    protected Generator $faker;
    private GetPlanByUuidService $planService;

    private Plan $plan;
    private PlanEntity $planEntity;

    public function setUp(): void
    {
        $loggerMock = $this->createMock(LoggerInterface::class);

        $this->planRepository = $this->getMockBuilder( PlanRepository::class )->disableOriginalConstructor()->getMock();
        $this->planService = new GetPlanByUuidService($this->planRepository, $loggerMock);

        $this->plan = $this->createPlan();
        $this->planEntity = $this->createPlanEntity($this->plan);
    }

    public function testPlanSearchByUUIDNotExist():void
    {
        $this->planRepository->expects($this->any())
            ->method('findByUuid')
            ->with($this->plan->uuid()->value())
            ->willReturn(null);

        $this->expectException(PlanNotExistException::class);
        $this->planService->__invoke($this->plan->uuid()->value());
    }
    //     php bin/phpunit tests/Plan/Application/Service/GetOnePlanByUuidTest.php

    public function testPlanSearchByUUIDOK():void
    {
        $this->planRepository->expects($this->any())
            ->method('findByUuid')
            ->with($this->plan->uuid()->value())
            ->willReturn($this->planEntity);

        $result = $this->planService->__invoke($this->planEntity->getUuid());
        $this->assertInstanceOf(PlanResponse::class , $result);
        $this->assertEquals($result->uuid(), $this->plan->uuid()->value());
        $this->assertEquals($result->name(), $this->plan->name()->value());
        $this->assertEquals($result->diskSize(), $this->plan->diskSize()->value());
        $this->assertEquals($result->type(), $this->plan->type()->value());
        $this->assertEquals($result->cores(), $this->plan->cores()->value());
        $this->assertEquals($result->memory(), $this->plan->memory()->value());
        $this->assertEquals($result->trafficLimit(), $this->plan->trafficLimit()->value());
    }
}
