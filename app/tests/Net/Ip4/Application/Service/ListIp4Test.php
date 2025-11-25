<?php
declare(strict_types=1);

namespace Tests\Net\Ip4\Application\Service;

use Faker\Factory as FakerFactory;
use Faker\Generator;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4Entity;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4NetworkEntity;
use GridCP\Net\Ip4\Application\Response\Ip4sResponse;
use GridCP\Net\Ip4\Application\Service\ListIp4Service;
use GridCP\Net\Ip4\Domain\Exceptions\ListIp4EmptyException;
use GridCP\Net\Ip4\Domain\Model\Ip4;
use GridCP\Net\Ip4\Infrastructure\DB\MySQL\Repository\Ip4Repository;
use PHPUnit\Framework\TestCase;

class ListIp4Test extends TestCase
{
    protected Ip4Repository $ip4Repository;
    protected Generator $faker;
    protected ListIp4Service $listIp4;
    public function setUp(): void
    {
        $this->ip4Repository = $this->getMockBuilder(Ip4Repository::class)->disableOriginalConstructor()->getMock();
        $this->listIp4 = new ListIp4Service($this->ip4Repository);
        $this->faker = FakerFactory::create();
    }

    public function testGetAllOK():void
    {
        $ip4Entity = new Ip4Entity();
        $ip4Entity->setUuid($this->faker->uuid());
        $ip4Entity->setIp($this->faker->ipv4());
        $networkEntity = new Ip4NetworkEntity();
        $networkEntity->setUuid($this->faker->uuid());
        $networkEntity->setName($this->faker->name());
        $ip4Entity->setNetwork ($networkEntity);
        $ip4Entity->setActive(true);
        
        $ip4Entity2 = new Ip4Entity();
        $ip4Entity2->setUuid($this->faker->uuid());
        $ip4Entity2->setIp($this->faker->ipv4());
        $networkEntity2 = new Ip4NetworkEntity();
        $networkEntity2->setUuid($this->faker->uuid());
        $networkEntity2->setName($this->faker->name());
        $ip4Entity2->setNetwork ($networkEntity2);
        $ip4Entity2->setActive(true);



        $this->ip4Repository->expects($this->any())
            ->method('getAll')
            ->willReturn([$ip4Entity,$ip4Entity2]);
        $result = $this->listIp4->__invoke();
        $this->assertInstanceOf(Ip4sResponse::class , $result);
    }

    public function testGetAllNotContent():void
    {
        $this->ip4Repository->expects($this->any())
            ->method('getAll')
            ->willReturn([]);
        $this->expectException(ListIp4EmptyException::class);
        $this->listIp4->__invoke();
    }
}// php bin/phpunit tests/Net/Ip4/Application/Service/ListIp4Test.php