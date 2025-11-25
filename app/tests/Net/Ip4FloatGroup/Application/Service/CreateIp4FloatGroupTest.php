<?php
declare(strict_types=1);

namespace Net\Ip4FloatGroup\Application\Service;

use Faker\Factory as FakerFactory;
use Faker\Generator;
use GridCP\Common\Domain\ValueObjects\UuidValueObject;
use GridCP\Net\Ip4FloatGroup\Application\Service\CreateIp4FloatGroup;
use GridCP\Net\Ip4FloatGroup\Domain\VO\Ip4FloatGroups;
use GridCP\Net\Ip4FloatGroup\Domain\VO\Ip4FloatGroupsActive;
use GridCP\Net\Ip4FloatGroup\Domain\VO\Ip4FloatGroupsName;
use GridCP\Net\Ip4FloatGroup\Domain\VO\Ip4FloatGroupsUuid;
use GridCP\Net\Ip4FloatGroup\Infrastructure\DB\MySQL\Entity\Ip4FloatGroupEntity;
use GridCP\Net\Ip4FloatGroup\Infrastructure\DB\MySQL\Repository\IpFloatGroupsRepository;
use PHPUnit\Framework\TestCase;

class CreateIp4FloatGroupTest extends TestCase
{
    protected IpFloatGroupsRepository $ipFloatGroupsRepository;

    private CreateIp4FloatGroup $createIp4FloatGroup;

    private Generator $faker;

    private Ip4FloatGroups $ip4FloatGroup;

    public function setUp(): void
    {
        $this->ipFloatGroupsRepository = $this->getMockBuilder(IpFloatGroupsRepository::class)->disableOriginalConstructor()->getMock();
        $this->createIp4FloatGroup = new CreateIp4FloatGroup($this->ipFloatGroupsRepository);
        $this->faker = FakerFactory::create();

        $ip4FloatGroupUUID = new Ip4FloatGroupsUuid(UuidValueObject::random()->value());
        $ip4FloatGroupName = new Ip4FloatGroupsName($this->faker->name());
        $ip4FloatGroupActive = new Ip4FloatGroupsActive(true);
        $this->ip4FloatGroup = new Ip4FloatGroups($ip4FloatGroupUUID, $ip4FloatGroupName, $ip4FloatGroupActive);
    }

    public function testCreateIp4FloatGroupOk(): void
    {
        $ip4FloatGroupEntity = new Ip4FloatGroupEntity();
        $ip4FloatGroupEntity->setName("Mock");
        $this->ipFloatGroupsRepository->expects($this->any())
            ->method('getByName')
            ->willReturn(null);
        $uuid = $this->createIp4FloatGroup->__invoke($this->ip4FloatGroup);
        $this->assertEquals($this->ip4FloatGroup->Uuid()->value(), $uuid);
    }

    public function testCreateIp4FloatGroupDuplicated(): void
    {
        $ip4FloatGroupEntity = new Ip4FloatGroupEntity();
        $ip4FloatGroupEntity->setName("Mock");
        $this->expectExceptionMessage('Ip4 Float Group Duplicated ->Mock');
        $this->ipFloatGroupsRepository->expects($this->any())
            ->method('getByName')
            ->willReturn($ip4FloatGroupEntity);
        $this->createIp4FloatGroup->__invoke($this->ip4FloatGroup);
        $this->expectExceptionCode(409);
    }

}