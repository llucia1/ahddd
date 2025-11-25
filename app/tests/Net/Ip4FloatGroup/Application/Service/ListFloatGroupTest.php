<?php
declare(strict_types=1);

namespace Net\Ip4FloatGroup\Application\Service;


use Faker\Factory as FakerFactory;
use Faker\Generator;
use GridCP\Net\Ip4FloatGroup\Application\Responses\FloatGroupResponses;
use GridCP\Net\Ip4FloatGroup\Application\Service\ListFloatGroup;
use GridCP\Net\Ip4FloatGroup\Domain\Exception\ListFloatGroupEmptyException;
use GridCP\Net\Ip4FloatGroup\Domain\Model\Ip4FloatGroupModel;
use GridCP\Net\Ip4FloatGroup\Domain\Repository\IIp4FloatGroupRepository;
use PHPUnit\Framework\TestCase;

class ListFloatGroupTest extends TestCase
{
    protected IIp4FloatGroupRepository $floatGroupRepository;
    protected Generator $faker;
    protected ListFloatGroup $listFloatGroup;
    public function setUp(): void
    {
        $this->floatGroupRepository = $this->getMockBuilder(IIp4FloatGroupRepository::class)->disableOriginalConstructor()->getMock();
        $this->listFloatGroup = new ListFloatGroup($this->floatGroupRepository);
        $this->faker = FakerFactory::create();
    }
    
    public function testGetAllOK():void
    {
        $floatGroup = new Ip4FloatGroupModel();
        $floatGroup->uuid =  $this->faker->uuid();
        $floatGroup->name=  $this->faker->name();
        $floatGroup->active =   $this->faker->boolean();

        $this->floatGroupRepository->expects($this->any())
            ->method('getAllActive')
            ->willReturn([$floatGroup]);
        $result = $this->listFloatGroup->__invoke();
        $this->assertInstanceOf(FloatGroupResponses::class , $result);
    }
    public function testGetAllNotContent():void
    {
        $this->floatGroupRepository->expects($this->any())
            ->method('getAllActive')
            ->willReturn([]);
        $this->expectException(ListFloatGroupEmptyException::class);
        $this->listFloatGroup->__invoke();

    }
}