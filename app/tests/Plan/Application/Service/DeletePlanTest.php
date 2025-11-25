<?php
declare(strict_types=1);

namespace Tests\Plan\Application\Service;

use GridCP\Plan\Application\Service\DeletePlanService;
use GridCP\Plan\Domain\VO\Plan;
use GridCP\Plan\Domain\VO\PlanName;
use GridCP\Plan\Domain\VO\PlanPatch;
use GridCP\Plan\Infrastructure\DB\MySQL\Entity\PlanEntity;
use GridCP\Plan\Infrastructure\DB\MySQL\Repository\PlanRepository;
use PHPUnit\Framework\TestCase;

use GuzzleHttp\Client;

use GridCP\Plan\Domain\VO\PlanUuid;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\Plan\Application\Helper\PlanTools;
use Faker\Factory as FakerFactory;

class DeletePlanTest extends TestCase
{

    protected PlanRepository $planRepository;

    private DeletePlanService $planService;
    protected static string $JWT;

    private PlanUuid $uuidPlan;
    private PlanEntity $planEntity;

    public function setUp(): void
    {
        $loggerMock = $this->createMock(LoggerInterface::class);

        $this->planRepository = $this->getMockBuilder( PlanRepository::class )->disableOriginalConstructor()->getMock();
        $this->planService = new DeletePlanService($this->planRepository, $loggerMock);


        $this->uuidPlan = new PlanUuid( PlanUuid::random()->value() );

        $faker = FakerFactory::create();
        $this->planEntity = new PlanEntity();
        $this->planEntity->setUuid($this->uuidPlan->value() );
        $this->planEntity->setName($faker->name());
        $this->planEntity->setDiskSize(10);
        $this->planEntity->setCores(2);
        $this->planEntity->setMemory(4096);
        $this->planEntity->settrafficlimit(8000);
    }

    public function testDeletePlanSuccess(): void
    {

        $this->planRepository->expects($this->once())
            ->method('findByUuid')
            ->with($this->uuidPlan->value())
            ->willReturn($this->planEntity);
            

        $this->planRepository->expects($this->once())
            ->method('delete')
            ->with($this->planEntity);

        $uuid = $this->planService->__invoke($this->uuidPlan);
        $this->assertEquals($uuid, $this->uuidPlan->value());
        
    }
    //     php bin/phpunit tests/Plan/Application/Service/DeletePlanTest.php

    public function testDeletePlanNotFound()
    {
    
        $this->planRepository->expects($this->once())
                ->method('findByUuid')
                ->with($this->uuidPlan->value())
                ->willReturn($this->planEntity);
        
            try {
                $this->planService->__invoke($this->uuidPlan);
            } catch (HttpException $e) {
                $this->assertSame(Response::HTTP_CONFLICT, $e->getStatusCode());
                $this->assertSame('Plan not found', $e->getMessage());
                return;
            }
        
    }

}