<?php
declare(strict_types=1);

namespace Proxmox\Vm\Application\Service;

use Faker\Factory as FakerFactory;
use Faker\Generator;
use GridCP\Common\Domain\Bus\Query\QueryBus;
use GridCP\Node\Application\Cqrs\Queries\SearchGroupNodeByIdQuerie;
use PHPUnit\Framework\TestCase;
use GridCP\Proxmox\Vm\Application\Helpers\ProxmoxVmFunctions;
use GridCP\Proxmox\Vm\Application\Response\CpuEntityResponse;
use GridCP\Proxmox\Vm\Application\Response\NodeEntityResponse;
use GridCP\Proxmox\Vm\Application\Response\VmEntityResponse;
use GridCP\Proxmox\Vm\Application\Service\GetAllVmService;
use GridCP\Proxmox\Vm\Domain\Exception\ListVmsEmptyException;
use GridCP\Proxmox\Vm\Domain\Repository\IVmRepository;
use GridCP\Proxmox\Vm\Infrastructure\DB\MySQL\Persistence\NodeEntity;
use GridCP\Proxmox\Vm\Infrastructure\DB\MySQL\Persistence\VmEntity;
use Psr\Log\LoggerInterface;

class ListAllVmTest extends TestCase
{
    protected Generator $faker;
    protected IVmRepository $vmRepository;
    protected LoggerInterface $logger;
    protected QueryBus $queryBusMock;

    protected GetAllVmService $listVms;
    use ProxmoxVmFunctions;

    public function setUp(): void
    {
        $this->vmRepository = $this->getMockBuilder(IVmRepository::class)->disableOriginalConstructor()->getMock();
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->queryBusMock = $this->createMock(QueryBus::class);
    //    $this->listVms = new GetAllVmService($this->vmRepository, $this->queryBusMock, $this->logger);
        $this->listVms = $this->createMock(GetAllVmService::class);

        $this->faker = FakerFactory::create();
    }

    private function getVmEntity(int $nodeId):VmEntity
    {
        $nodeId = $nodeId;
        $vmEntity = new VmEntity();
        $vmEntity->setId(1);
        $vmEntity->setUuid($this->faker->uuid());
        $vmEntity->setName($this->faker->name());
        $vmEntity->setCores($this->faker->randomNumber(2));
        $vmEntity->setCpu($this->faker->name());
        $vmEntity->setOs($this->faker->name());
        $vmEntity->setActive(true); 

        $vmEntity->setIdNode($nodeId);

        return $vmEntity;
    }


    private function getNode(int $nodeId): ?NodeEntityResponse
    {
            return new NodeEntityResponse(
                                        $nodeId,
                                        $this->faker->uuid(),
                                        $this->faker->name(),
                                        $this->faker->name(),
                                        $this->faker->name(),
                                        $this->faker->userName(),
                                        $this->faker->password(),
                                        $this->faker->name(),
                                        $this->faker->randomNumber(4),
                                        $this->faker->ipv4(),
                                        $this->faker->randomNumber(4),
                                        $this->faker->timezone(),
                                        $this->faker->randomLetter(),
                                        $this->faker->text(20),
                                        $this->faker->text(20),
                                        $this->faker->text(20),
                                        $this->faker->text(20),
                                        $this->faker->text(20),
                                        $this->faker->text(20),
                                        new CpuEntityResponse(
                                                               $this->faker->name(),
                                                               $this->faker->name(),
                                                               $this->faker->randomNumber(2)
                                                             )
    
                                        
                                        );
    }
    
    public function toResponses(array $vms):array
    {
        $result = [];
        foreach ($vms as $vm) {

            $vmResponse = new VmEntityResponse(
                $vm->id(),
                $vm->uuid(),
                $vm->name(),
                $this->getNode($vm->idNode()),
                $vm->cpu(),
                $vm->cores(),
                $vm->active(),
                $vm->os()
            );
            $result[] = $vmResponse;
        }
        return $result;
    }

    public function testAllVmsOK():void
    {
        $arrayNodeIds = [1,2];
        $arrayVmEntities = [];
        $arrayVmEntities[] = $this->getVmEntity($arrayNodeIds[0]);
        $arrayVmEntities[] = $this->getVmEntity($arrayNodeIds[1]);
        
        $this->vmRepository->expects($this->any())
            ->method('getAll')
            ->willReturn($arrayVmEntities);
            
        $response = $this->toResponses( $arrayVmEntities );
        
        $this->listVms->method('__invoke')->willReturn($response);

        $this->assertIsArray($response);
        $this->assertCount(2, $response);
        

    }

    public function testAllVmEmpty():void
    {
        $this->vmRepository->expects($this->any())
                           ->method('getAll')
                           ->willReturn([]);

        $this->expectException(ListVmsEmptyException::class);
        $this->listVms->__invoke();
        $this->expectException(ListVmsEmptyException::class);
        $vms = new GetAllVmService($this->vmRepository, $this->queryBusMock, $this->logger);
        $vms->__invoke();
    }
    // php bin/phpunit tests/Proxmox/Vm/Application/Service/ListAllVmTest.php
}