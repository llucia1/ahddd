<?php
declare(strict_types=1);

namespace Tests\Plan\Application\Service;

use GridCP\Plan\Application\Service\CreatePlanService;
use GridCP\Plan\Domain\VO\Plan;
use GridCP\Plan\Infrastructure\DB\MySQL\Entity\PlanEntity;
use GridCP\Plan\Infrastructure\DB\MySQL\Repository\PlanRepository;
use PHPUnit\Framework\TestCase;



use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\Plan\Application\Helper\PlanTools;

class PostPlanTest extends TestCase
{

    use PlanTools;

    protected PlanRepository $planRepository;

    private CreatePlanService $planService;

    private Plan $plan;
    private PlanEntity $planEntity;

    public function setUp(): void
    {
        $loggerMock = $this->createMock(LoggerInterface::class);

        $this->planRepository = $this->getMockBuilder( PlanRepository::class )->disableOriginalConstructor()->getMock();
        $this->planService = new CreatePlanService($this->planRepository, $loggerMock);

        $this->plan = $this->createPlan();
        $this->planEntity = $this->createPlanEntity($this->plan);
    }

    public function testPostPlanSuccess(): void
    {

        $this->planRepository->expects($this->once())
            ->method('findByUuid')
            ->with($this->plan->uuid()->value())
            ->willReturn(null);

        $this->planRepository->expects($this->once())
            ->method('save')
            ->with($this->planEntity);

        $uuid = $this->planService->__invoke($this->plan);
        $this->assertEquals($uuid, $this->planEntity->getUuid());
        
    }
    //     php bin/phpunit tests/Plan/Application/Service/PostPlanTest.php
}
