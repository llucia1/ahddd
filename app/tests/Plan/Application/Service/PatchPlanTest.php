<?php
declare(strict_types=1);

namespace Tests\Plan\Application\Service;

use GridCP\Plan\Application\Service\CreatePlanService;
use GridCP\Plan\Application\Service\PatchPlanService;
use GridCP\Plan\Domain\VO\Plan;
use GridCP\Plan\Domain\VO\PlanName;
use GridCP\Plan\Domain\VO\PlanPatch;
use GridCP\Plan\Infrastructure\DB\MySQL\Entity\PlanEntity;
use GridCP\Plan\Infrastructure\DB\MySQL\Repository\PlanRepository;
use PHPUnit\Framework\TestCase;

use GuzzleHttp\Client;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\Plan\Application\Helper\PlanTools;
use Faker\Factory as FakerFactory;

class PatchPlanTest extends TestCase
{

    use PlanTools;

    protected PlanRepository $planRepository;

    private PatchPlanService $planService;
    protected static string $JWT;

    private PlanPatch $plan;
    private PlanEntity $planEntity;

    public function setUp(): void
    {
        $loggerMock = $this->createMock(LoggerInterface::class);

        $this->planRepository = $this->getMockBuilder( PlanRepository::class )->disableOriginalConstructor()->getMock();
        $this->planService = new PatchPlanService($this->planRepository, $loggerMock);

        $this->plan = $this->createPlanPatch();// plan a editarvalores que se desean editar del plan



        $faker = FakerFactory::create();
        /*   ENCONTRADO Y AL QUE SE LE APLICARAN LOS VALORES A EDITAR */
        $this->planEntity = new PlanEntity();
        $this->planEntity->setUuid($this->plan->uuid()->value());
        $this->planEntity->setName($faker->name());
        $this->planEntity->setDiskSize(10);
        $this->planEntity->setCores(2);
        $this->planEntity->setMemory(4096);
        $this->planEntity->settrafficlimit(8000);
    }

    public function testPatchPlanSuccess(): void
    {

        $this->planRepository->expects($this->once())
            ->method('findByUuid')
            ->with($this->plan->uuid()->value())
            ->willReturn($this->planEntity);
            

        $this->planRepository->expects($this->once())
            ->method('save')
            ->with($this->planEntity);

        $uuid = $this->planService->__invoke($this->plan, $this->planEntity->getUuid());
        $this->assertEquals($uuid, $this->plan->uuid()->value());
        
    }
    //     php bin/phpunit tests/Plan/Application/Service/PatchPlanTest.php

    public function testPatchPlanErrorDuplicate()
    {
        $USER_LOGIN = [
            "email" => "xavi@xavi.com",
            "password" => "password",
        ];
        $client = new Client(['base_uri'=>'http://localhost:80']);
        $result = $client->post('/api/v1/auth/login',['json'=>$USER_LOGIN]);
        $JWT =   json_decode($result->getBody()->getContents())->token;

        $client = new Client(['base_uri' => 'http://localhost:80', 'headers'=>['Authorization'=>'Bearer '.$JWT]]);
        $result = $client->get('/api/v1/plan');
        $plan = json_decode($result->getBody()->getContents(),true);
        $planExits = $plan[0];

        $planPatch = PlanPatch::create(
            $this->plan->uuid(),
             new PlanName($planExits['name']),// Paso un name que ya existe, encontrado anteriormente
            $this->plan->diskSize(),
            $this->plan->cores(),
            $this->plan->memory(),
            $this->plan->trafficLimit(),
        );

        $this->planRepository->expects($this->once())
                ->method('findByUuid')
                ->with($this->plan->uuid()->value())
                ->willReturn($this->planEntity);
        
            try {
                $this->planService->__invoke($planPatch, $this->planEntity->getUuid());
            } catch (HttpException $e) {
                $this->assertSame(Response::HTTP_CONFLICT, $e->getStatusCode());
                $this->assertSame('Plan Error. Duplicate. -> '.$this->plan->name()->value(), $e->getMessage());
                return;
            }
        
    }
}
