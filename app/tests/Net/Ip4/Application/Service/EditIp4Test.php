<?php
declare(strict_types=1);

namespace Tests\Net\Ip4\Application\Service;

use Faker\Factory as FakerFactory;
use Faker\Generator;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4NetworkEntity;
use GridCP\Net\Common\Application\Helpers\CalcIps;

use GridCP\Net\Ip4\Application\Service\EditIP4service;

use GridCP\Net\Ip4\Domain\VO\Ip4Ip;

use GridCP\Net\Ip4\Domain\VO\Ip4UuidNetwork;

use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4Entity;
use GridCP\Net\Ip4\Domain\Exceptions\NerworkNoExist;
use GridCP\Net\Ip4\Domain\VO\PatchIp4Vo;
use GridCP\Net\Ip4\Infrastructure\DB\MySQL\Repository\Ip4Repository;
use IPCalc\IP;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class EditIp4Test extends TestCase
{
    use CalcIps;
    protected Generator $faker;

    protected Ip4Repository $ip4Repository;

    private EditIP4service $editIp4;

    private PatchIp4Vo $ip4Vo;


    private int $cidr = 32;
    private string $uuidNetwork = 'd0b9c9c0-5b1e-4e1a-8b1a-0e2e8c0f8c0e';
    private string $ip4 = '192.168.2.0'.'/';
    private string $uuid = '13e64480-2108-4640-850c-24eea1cb9254';



    public function setUp(): void
    {
        $this->ip4Repository = $this->getMockBuilder(Ip4Repository::class)->disableOriginalConstructor()->getMock();
        $logger = $this->createMock( LoggerInterface::class );

        $this->editIp4 = new EditIP4service($this->ip4Repository, $logger);


        $this->faker = FakerFactory::create();


        $this->ip4 = $this->ip4.$this->cidr;



    }
    // php bin/phpunit tests/Net/Ip4/Application/Service/EditIp4Test.php
    public function testeditIp4Success(): void
    {

        $ip4UuidNetwork = new Ip4UuidNetwork($this->uuidNetwork);
        $ip4Ip = new Ip4Ip($this->ip4);
        $this->ip4Vo = new PatchIp4Vo($ip4Ip,$ip4UuidNetwork);
        

        $networkEntity = new Ip4NetworkEntity();
        $networkEntity->setUuid($ip4UuidNetwork->value());
        $networkEntity->setName($this->faker->name());
        

        $ipCal = new IP($this->ip4);
        $ip4s = $this->getIp4s($ipCal);


        
        $this->ip4Repository->expects($this->once())
                                            ->method('existIdNetwork')
                                            ->with($ip4UuidNetwork->value())
                                            ->willReturn($networkEntity);


        foreach ($ip4s as $ipx)
        {
            $ip4Entity = new Ip4Entity();
            $ip4Entity->setUuid($this->uuid);
            $ip4Entity->setIp($ipx);
            $ip4Entity->setActive(true);
            $ip4Entity->setNetwork($networkEntity);

            $this->ip4Repository->expects($this->atLeastOnce())
                                ->method('findByIP')
                                ->with($ip4Entity->getIp())
                                ->willReturn($ip4Entity);

            $this->ip4Repository->expects($this->atLeastOnce())
                                ->method('save')
                                ->with($ip4Entity);
        }
        $resultservice = $this->editIp4->__invoke($this->ip4Vo);
        $this->assertIsArray($resultservice);
    }
    public function testeditIp4NotFound(): void
    {

        $ip4UuidNetwork = new Ip4UuidNetwork($this->uuidNetwork);
        $ip4Ip = new Ip4Ip($this->ip4);
        $this->ip4Vo = new PatchIp4Vo($ip4Ip,$ip4UuidNetwork);
        

        $networkEntity = new Ip4NetworkEntity();
        $networkEntity->setUuid($ip4UuidNetwork->value());
        $networkEntity->setName($this->faker->name());
        

        $ipCal = new IP($this->ip4);
        $ip4s = $this->getIp4s($ipCal);


        
        $this->ip4Repository->expects($this->once())
                                            ->method('existIdNetwork')
                                            ->with($ip4UuidNetwork->value())
                                            ->willReturn($networkEntity);

        $result = [];
        foreach ($ip4s as $ipx)
        {
            $ip4Entity = new Ip4Entity();
            $ip4Entity->setUuid($this->uuid);
            $ip4Entity->setIp($ipx);
            $ip4Entity->setActive(true);
            $ip4Entity->setNetwork($networkEntity);

            $this->ip4Repository->expects($this->atLeastOnce())
                                ->method('findByIP')
                                ->with($ip4Entity->getIp())
                                ->willReturn(null);
            $result[] = $ipx;

        }

        $resultservice = $this->editIp4->__invoke($this->ip4Vo);

        $this->assertIsArray($resultservice);

        $this->assertEquals($resultservice[0], $ip4s[0]);
    }
    public function testeditIp4WithNetworkNotFound(): void
    {

        $ip4UuidNetwork = new Ip4UuidNetwork($this->uuidNetwork);
        $ip4Ip = new Ip4Ip($this->ip4);
        $this->ip4Vo = new PatchIp4Vo($ip4Ip,$ip4UuidNetwork);
        

        $networkEntity = new Ip4NetworkEntity();
        $networkEntity->setUuid($ip4UuidNetwork->value());
        $networkEntity->setName($this->faker->name());
        

        $ipCal = new IP($this->ip4);
         $this->getIp4s($ipCal);


        
        $this->ip4Repository->expects($this->once())
                                            ->method('existIdNetwork')
                                            ->with($ip4UuidNetwork->value())
                                            ->willReturn(null);

        $this->expectException(NerworkNoExist::class);

        $this->editIp4->__invoke($this->ip4Vo);
        
    }
}
