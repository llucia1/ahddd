<?php
declare(strict_types=1);

namespace Tests\Net\Ip4\Application\Service;

use Faker\Factory as FakerFactory;
use Faker\Generator;
use GridCP\Net\Common\Application\Helpers\CalcIps;

use GridCP\Net\Ip4\Application\Service\DeleteIP4;
use GridCP\Net\Ip4\Common\Service\DeleteIP4Common;
use GridCP\Net\Ip4\Domain\VO\Ip4Ip;
use GridCP\Net\Ip4\Domain\VO\Ip4Ips;
use GridCP\Net\Ip4\Infrastructure\DB\MySQL\Repository\Ip4Repository;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class DeleteIp4Test extends TestCase
{
    const VALUENULL = null;
    use CalcIps;
    protected Generator $faker;

    protected Ip4Repository $ip4Repository;

    private DeleteIP4 $deleteIp4;
    private DeleteIP4Common $deleteIp4Common;

    private Ip4Ip $ip4Vo;
    private Ip4Ips $ip4sVo;
    private string $ip4;
    private array $ip4NotFound;

    public function setUp(): void
    {
        $this->ip4Repository = $this->getMockBuilder(Ip4Repository::class)->disableOriginalConstructor()->getMock();
        $logger = $this->createMock( LoggerInterface::class );

        $this->deleteIp4Common = $this->createMock(DeleteIP4Common::class);
        $this->deleteIp4 = new DeleteIP4($this->ip4Repository, $this->deleteIp4Common,$logger);


        $this->faker = FakerFactory::create();
        $this->ip4 = '192.168.2.0';

        $this->ip4Vo = new Ip4Ip($this->ip4 );

        $this->ip4sVo = new Ip4Ips([$this->ip4 ]);

        $this->ip4NotFound = [$this->ip4];
        
        
    }
    
    public function testDeleteIP4Success(): void
    {
        
        $this->deleteIp4Common->expects($this->once())
                                            ->method('deleteIP4s')
                                            ->with($this->ip4sVo)
                                            ->willReturn(null);




        $result = $this->deleteIp4->__invoke($this->ip4sVo);
        
        $this->assertEquals ( $result , self::VALUENULL);
    }
/*

php bin/phpunit tests/Net/Ip4/Application/Service/DeleteIp4Test.php
*/
    public function testDeleteIP4NotFound(): void
    {
              

        $this->deleteIp4Common->expects($this->once())
                                            ->method('deleteIP4s')
                                            ->with($this->ip4sVo)
                                            ->willReturn($this->ip4NotFound );




        $result = $this->deleteIp4->__invoke($this->ip4sVo);

        $this->assertIsArray( $result );

        $this->assertEquals( $result , $this->ip4NotFound);

    }

}