<?php
declare(strict_types=1);

namespace Tests\Net\Ip4\Application\Service;

use Faker\Factory as FakerFactory;
use Faker\Generator;
use GridCP\Net\Ip4\Application\Response\Ip4Response;
use GridCP\Net\Ip4\Application\Service\ListIp4ByNetworkUuid;
use GridCP\Net\Ip4\Domain\VO\Ip4UuidNetwork;

use GridCP\Net\Ip4\Infrastructure\DB\MySQL\Repository\Ip4Repository;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use GridCP\Common\Domain\Bus\Query\QueryBus;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4Entity;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4NetworkEntity;
use GridCP\Net\Ip4\Application\Response\Ip4sResponse;
use GridCP\Net\Ip4\Domain\Exceptions\NerworkNoExistException;
use GridCP\Net\Ip4\Domain\Repository\IIp4Repository;
use GridCP\Net\Ip4FloatGroup\Domain\Model\Ip4FloatGroupModel;
use GridCP\Net\Ip4Network\Application\Cqrs\Queries\GetNetworkByUuidQueried;
use GridCP\Net\Ip4Network\Application\Responses\Ip4NetworkQueryResponse;
use GridCP\Net\Ip4Network\Application\Responses\Ip4NetworkResponse;

class ListIp4ByNetworkUuidTest extends TestCase
{
    protected IIp4Repository $ip4Repository;
    protected Generator $faker;
    private LoggerInterface $loggerMock;
    private QueryBus $queryBusMock;
    

    protected ListIp4ByNetworkUuid $listIp4ByNetworkUuid;

    public function setUp(): void
    {
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->queryBusMock = $this->createMock(QueryBus::class);

        // Crea un mock de la interfaz IIp4Repository
        $this->ip4Repository = $this->createMock(IIp4Repository::class);

        $this->listIp4ByNetworkUuid = new ListIp4ByNetworkUuid($this->ip4Repository, $this->loggerMock, $this->queryBusMock);


        $this->faker = FakerFactory::create();
    }

    public function testIp4SearchByNetworkUUIDNotExist():void
    {
        $networkUuid = new Ip4UuidNetwork($this->faker->uuid());
        $this->queryBusMock->expects($this->once())
            ->method('ask')
            ->with($this->isInstanceOf(GetNetworkByUuidQueried::class))
            ->willThrowException(new \Exception('Network not found'));


        $this->expectException(NerworkNoExistException::class);
        $this->listIp4ByNetworkUuid->__invoke( $networkUuid );
    }

    public function testIp4SearchByNetworkUUIDOK():void
    {
        $networkUuid = new Ip4UuidNetwork($this->faker->uuid());
        $networId = 1;

        $floatGroup = new Ip4FloatGroupModel();
        $floatGroup->id = 1;
        $floatGroup->uuid = $this->faker->uuid();
        $floatGroup->name = $this->faker->name();
        $floatGroup->active = true;
        
        $networkResponse = new Ip4NetworkResponse(
                                                    $networkUuid->value(),
                                                    $this->faker->name(),
                                                    $this->faker->name(),
                                                    $this->faker->name(),
                                                    $this->faker->name(),
                                                    $this->faker->name(),
                                                    8,
                                                    $this->faker->ipv4(),
                                                    $this->faker->ipv4(),
                                                    $this->faker->ipv4(),
                                                    $floatGroup,
                                                    $networId
                                                );

        $networkQuery = new Ip4NetworkQueryResponse($networkResponse);
        $this->queryBusMock->expects($this->once())
            ->method('ask')
            ->with($this->isInstanceOf(GetNetworkByUuidQueried::class)) // Asegúrate de que el argumento sea una instancia de GetNetworkByUuidQueried
            ->willReturn($networkQuery);
            


        $networkEnity = new Ip4NetworkEntity();
        $networkEnity->setActive(true);
        $networkEnity->setName($networkResponse->name() );
        $networkEnity->setUuid($networkResponse->uuid() );
            
         $ip4Entity1 = new Ip4Entity();
         $ip4Entity1->setUuid($this->faker->uuid());
         $ip4Entity1->setIp($this->faker->ipv4());
         $ip4Entity1->setNetwork($networkEnity);
         $ip4Entity1->setActive(true);

         $ip4Entity2 = new Ip4Entity();
         $ip4Entity2->setUuid($this->faker->uuid());
         $ip4Entity2->setIp($this->faker->ipv4());
         $ip4Entity2->setNetwork($networkEnity);
         $ip4Entity2->setActive(true);

        $this->ip4Repository->expects($this->once())
                ->method('findAllByNetworkid')
                ->with($networkQuery->get()->id()) // Asegurarse de que se llame con el ID de red correcto
                ->willReturn([$ip4Entity1, $ip4Entity2]); // Devolver la lista de IPs

        $result = $this->listIp4ByNetworkUuid->__invoke($networkUuid);


        $this->assertInstanceOf(Ip4sResponse::class , $result);

        $resultArray = $result->ip4s();
        
        $this->assertEquals($resultArray[0]->uuid(), $ip4Entity1->getUuid());

        $this->assertEquals($resultArray[1]->uuid(), $ip4Entity2->getUuid());

    }
}
    // php bin/phpunit tests/Net/Ip4/Application/Service/ListIp4ByNetworkUuidTest.php
