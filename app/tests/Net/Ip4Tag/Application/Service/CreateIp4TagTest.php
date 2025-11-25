<?php
declare(strict_types=1);

namespace Net\Ip4Tag\Application\Service;

use Faker\Factory as FakerFactory;
use Faker\Generator;
use GridCP\Common\Domain\ValueObjects\UuidValueObject;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4TagEntity;
use GridCP\Net\Ip4\Application\Service\CreateIP4;
use GridCP\Net\Ip4\Infrastructure\DB\MySQL\Repository\Ip4Repository;
use GridCP\Net\Ip4Tag\Application\Service\CreateIP4Tag;
use GridCP\Net\Ip4Tag\Domain\VO\Ip4Tag;
use GridCP\Net\Ip4Tag\Domain\VO\Ip4TagIdIp;
use GridCP\Net\Ip4Tag\Domain\VO\Ip4TagTag;
use GridCP\Net\Ip4Tag\Domain\VO\Ip4TagUuid;
use GridCP\Net\Ip4Tag\Infrastructure\DB\MySQL\Repository\Ip4TagRepository;
use PHPUnit\Framework\TestCase;

class CreateIp4TagTest extends TestCase
{
    protected Ip4TagRepository $ip4TagRepository;
    private Ip4Repository $ip4Repository;
    private CreateIP4 $createIp4Tag;
    private Generator $faker;
    private Ip4Tag $ip4TagVO;

    public function setUp(): void
    {
        $this->ip4TagRepository = $this->getMockBuilder(Ip4TagRepository::class)->disableOriginalConstructor()->getMock();
        $this->ip4Repository = $this->getMockBuilder(Ip4Repository::class)->disableOriginalConstructor()->getMock();
        $this->createIp4Tag = new CreateIp4Tag($this->ip4TagRepository, $this->ip4Repository);
        $this->faker = FakerFactory::create();

        $ip4TagUuid = new Ip4TagUuid(UuidValueObject::random()->value());
        $ip4IdIp4Tag = new Ip4TagIdIp($this->faker->randomNumber(1));
        $ip4TagTag = new Ip4TagTag($this->faker->name());

        $this->ip4TagVO = new Ip4Tag($ip4TagUuid, $ip4IdIp4Tag, $ip4TagTag);
    }

        public function testCreateIp4TagOk(): void
        {
            $ip4TagEntity = new Ip4TagEntity();
            $ip4TagEntity->setUuid($this->ip4TagVO->Uuid()->value());
            $this->ip4TagRepository->expects($this->any())
                ->method('save');
            $uuid = $this->createIp4Tag->__invoke($this->ip4TagVO);
            $this->assertEquals($this->ip4TagVO->Uuid()->value(), $uuid);
        }

    public function testErrorCreateTagDuplicated(): void
    {
        $ip4TagEntity = new Ip4TagEntity();
        $uuidMock = $this->faker->uuid();
        $ip4TagEntity->setUuid($uuidMock);
        $this->expectExceptionMessage("Ip4 Tag Duplicated ->" . $uuidMock);
        $this->ip4TagRepository->expects($this->any())
            ->method('getByUuid')
            ->willReturn($ip4TagEntity);
        $this->createIp4Tag->__invoke($this->ip4TagVO);
        $this->expectExceptionCode(409);
    }
}