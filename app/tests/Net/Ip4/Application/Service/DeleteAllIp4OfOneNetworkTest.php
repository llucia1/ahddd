<?php
declare(strict_types=1);

namespace Tests\Net\Ip4\Application\Service;

use Faker\Factory as FakerFactory;
use Faker\Generator;
use GridCP\Net\Common\Application\Helpers\CalcIps;
use GridCP\Net\Ip4\Application\Service\CreateIP4;
use GridCP\Net\Ip4\Application\Service\DeleteAllIp4OfNetwork;

use GridCP\Net\Ip4\Domain\VO\Ip4;

use GridCP\Common\Domain\Bus\Query\QueryBus;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4Entity;
use GridCP\Net\Ip4\Common\Service\DeleteIP4Common;
use GridCP\Net\Ip4\Domain\Exceptions\NerworkNoExistException;
use GridCP\Net\Ip4\Domain\VO\Ip4Ip;
use GridCP\Net\Ip4\Domain\VO\Ip4Ips;
use GridCP\Net\Ip4\Domain\VO\Ip4UuidNetwork;
use GridCP\Net\Ip4\Infrastructure\DB\MySQL\Repository\Ip4Repository;
use GridCP\Net\Ip4Network\Application\Cqrs\Queries\GetNetworkByUuidQueried;
use GridCP\Net\Ip4Network\Application\Responses\Ip4NetworkQueryResponse;
use GridCP\Net\Ip4Network\Application\Responses\Ip4NetworkResponse;
use GridCP\Net\Ip4Network\Domain\Exception\ListIp4NetworkEmptyException;
use IPCalc\IP;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class DeleteAllIp4OfOneNetworkTest extends TestCase
{
    const VALUENULL = null;
    use CalcIps;
    protected Generator $faker;

    protected Ip4Repository $ip4Repository;
    protected QueryBus $queryBusMock;
    private DeleteAllIp4OfNetwork $deleteIp4All;
    private DeleteIP4Common $deleteIp4Common;

    private Ip4UuidNetwork $ip4UuidNetwork;
    private Ip4Ip $ip4Vo;
    private Ip4Ips $ip4sVo;
    private string $ip4;
    private array $ip4NotFound;

    public function setUp(): void
    {
        $this->ip4Repository = $this->getMockBuilder(Ip4Repository::class)->disableOriginalConstructor()->getMock();
        $logger = $this->createMock( LoggerInterface::class );
        $this->queryBusMock = $this->createMock(QueryBus::class);

        $this->deleteIp4Common = $this->createMock(DeleteIP4Common::class);
        $this->deleteIp4All = new DeleteAllIp4OfNetwork($this->ip4Repository, $this->deleteIp4Common,$logger, $this->queryBusMock);


        $this->faker = FakerFactory::create();
        $this->ip4UuidNetwork = new Ip4UuidNetwork($this->faker->uuid());
        $this->ip4 = '192.168.2.0';

        $this->ip4Vo = new Ip4Ip($this->ip4 );

        $this->ip4sVo = new Ip4Ips([$this->ip4 ]);

        $this->ip4NotFound = [$this->ip4];
        
        
    }
    
    public function testDeleteIP4AllSuccess(): void
    {

        $reponseCqrsNerwork = new Ip4NetworkResponse(
            $this->ip4UuidNetwork->value(),
            $this->faker->name(),
            $this->faker->ipv4,
            $this->faker->ipv4,
            $this->faker->ipv4,
            $this->faker->ipv4,
            1,
            $this->faker->ipv4,
            $this->faker->ipv4,
            $this->faker->ipv4,
            null,
            1,
        );


        $this->queryBusMock->expects($this->any())
                 ->method('ask')
                 ->with( new GetNetworkByUuidQueried($this->ip4UuidNetwork->value()) )
                 ->willReturn( new Ip4NetworkQueryResponse( $reponseCqrsNerwork ));

        $ipEntity = new Ip4Entity();
        $ipEntity->setUuid($this->faker->uuid());
        $ipEntity->setIp($this->ip4Vo->value());
        
        $arrayEntityIp = [
            $ipEntity

        ];
        $this->ip4Repository->expects($this->once())
                                                     ->method('findAllByNetworkid')
                                                     ->with($reponseCqrsNerwork->id())
                                                     ->willReturn($arrayEntityIp);

        $this->deleteIp4Common->expects($this->once())
                                            ->method('deleteIP4s')
                                            ->with($this->ip4sVo);




        $this->deleteIp4All->__invoke($this->ip4UuidNetwork );
    }
/*

php bin/phpunit tests/Net/Ip4/Application/Service/DeleteAllIp4OfOneNetworkTest.php
*/
    public function testDeleteIP4AllNetworkNotFound(): void
    {
              

        $this->queryBusMock->expects($this->any())
        ->method('ask')
        ->with(new GetNetworkByUuidQueried($this->ip4UuidNetwork->value()))
        ->willThrowException(new ListIp4NetworkEmptyException());

        $this->expectException(NerworkNoExistException::class);
                                            

        $this->deleteIp4All->__invoke($this->ip4UuidNetwork );

    }

}