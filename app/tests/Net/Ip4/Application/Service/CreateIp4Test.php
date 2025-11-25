<?php
declare(strict_types=1);

namespace Tests\Net\Ip4\Application\Service;

use Faker\Factory as FakerFactory;
use Faker\Generator;
use GridCP\Common\Domain\Bus\Query\QueryBus;
use GridCP\Net\Common\Application\Helpers\CalcIps;

use GridCP\Net\Ip4\Application\Response\Ip4Response;
use GridCP\Net\Ip4\Application\Service\CreateIP4;
use GridCP\Net\Ip4\Domain\VO\Ip4;

use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4Entity;
use GridCP\Net\Ip4\Domain\Exceptions\Ip4Duplicated;
use GridCP\Net\Ip4\Domain\Exceptions\NerworkNoExist;
use GridCP\Net\Ip4\Infrastructure\DB\MySQL\Repository\Ip4Repository;
use IPCalc\IP;
use Tests\Net\Common\Application\Helpers\IpsTestTrait;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class CreateIp4Test extends TestCase
{
    use CalcIps, IpsTestTrait;
    protected Generator $faker;

    protected Ip4Repository $ip4Repository;

    private CreateIp4 $createIp4;
    private LoggerInterface $logger;
    private QueryBus $queryBus;

    private Ip4 $ip4Vo;
    private int $idNetwork;

    public function setUp(): void
    {
        $this->ip4Repository = $this->getMockBuilder(Ip4Repository::class)->disableOriginalConstructor()->getMock();
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->queryBus = $this->createMock(QueryBus::class);

        $this->createIp4 = new CreateIp4($this->ip4Repository, $this->logger, $this->queryBus );


        $this->faker = FakerFactory::create();

        $cidr = 32;
        $uuidNetwork = 'd0b9c9c0-5b1e-4e1a-8b1a-0e2e8c0f8c0e';
        $this->idNetwork = 1;// NOSONAR
        $ip4 = '192.168.2.0';// NOSONAR
        $uuid = '13e64480-2108-4640-850c-24eea1cb9254';

        $this->ip4Vo = $this->ip4Vo(
            $cidr,
            $uuidNetwork,
            $this->idNetwork,
            $ip4,
            $uuid,
            1,
            'Not set'

        ); 
    }
    // php bin/phpunit Tests/Net/Ip4/Application/Service/CreateIp4Test.php
    public function testCreateIp4Success(): void
    {        
        $networkEntity = $this->ip4NetworkEntity($this->ip4Vo, $this->faker);

        $ipCal = new IP($this->ip4Vo->Ip4()->value());
        $ip4s = $this->getIp4s($ipCal);
        
        $result = $this->ip4Entitys($this->ip4Vo, $networkEntity);
        
        $this->ip4Repository->expects($this->once())
                                            ->method('existIdNetwork')
                                            ->with( $this->ip4Vo->UuidNetwork()->value() )
                                            ->willReturn($networkEntity);

        $this->ip4Repository->method('findByIP')
            ->with($this->callback(fn($ip) => in_array($ip, $ip4s)))
            ->willReturn(null);

                                            $idNetwork = $this->idNetwork;
                                            $this->ip4Repository->expects($this->atLeastOnce())
                                            ->method('save')
                                            ->with($this->callback(function ($entity) use ( $idNetwork , $ip4s) {// NOSONAR
                                                return $entity instanceof Ip4Entity
                                                    && in_array($entity->getIp(), $ip4s)
                                                    && $entity->isActive() === true;
                                            }));
                                            
        $resultservice = $this->createIp4->__invoke($this->ip4Vo);

        

        $this->assertIsArray($resultservice);
        $this->assertInstanceOf(Ip4Response::class , $resultservice[0]);
        $this->assertInstanceOf(Ip4Entity::class , $result[0]);
    }
    public function testCreateIp4NotFoundNetwork(): void
    {
        
        $this->ip4Repository->expects($this->once())
                                            ->method('existIdNetwork')
                                            ->with( $this->ip4Vo->UuidNetwork()->value() )
                                            ->willReturn(null);

        $this->expectException(NerworkNoExist::class);
        $this->expectExceptionMessage($this->ip4Vo->UuidNetwork()->value());

        $this->createIp4->__invoke($this->ip4Vo);
    }


    public function testCreateIp4Duplicated(): void
    {
        $networkEntity = $this->ip4NetworkEntity($this->ip4Vo, $this->faker);   

        $ipCal = new IP($this->ip4Vo->Ip4()->value());
        $ip4s = $this->getIp4s($ipCal); 

        $this->ip4Repository->method('existIdNetwork')
            ->with($this->ip4Vo->UuidNetwork()->value())
            ->willReturn($networkEntity);   

        $this->ip4Repository->method('findByIP')
            ->with($this->callback(function ($ip) use ($ip4s) {
                return in_array($ip, $ip4s);
            }))
            ->willReturn(new Ip4Entity());  

        $this->expectException(Ip4Duplicated::class);   

        $this->createIp4->__invoke($this->ip4Vo);
    }



}
