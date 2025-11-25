<?php
declare(strict_types=1);

namespace Proxmox\Version\Application\Service;

use Faker\Generator;
use GridCP\Common\Domain\Bus\Query\QueryBus;
use PHPUnit\Framework\TestCase;



use GridCP\Proxmox\Common\Infrastructure\ProxmoxClientService;
use GridCP\Proxmox\Version\Application\Response\VersionResponse;
use GridCP\Proxmox\Version\Application\Service\GetVersionService;
use GridCP\Proxmox\Version\Domain\Services\IGetVersionService;
use Psr\Log\LoggerInterface;

class GetVersionServiceTest extends TestCase
{
    protected Generator $faker;

    private $proxmoxClientServiceMock;
    private $loggerMock;
    private $queryBusMock;
    private $versionData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->queryBusMock = $this->createMock(QueryBus::class);

        $this->proxmoxClientServiceMock = $this->getMockBuilder(ProxmoxClientService::class)
        ->disableOriginalConstructor()
        ->getMock();

        $this->versionData = new VersionResponse(
                                                    "8.1",
                                                    "ec5affc9e41f1d79",
                                                    "8.1.4"
                                                );


    }

    
    public function testGetVersionServiceReturnVersionkResponses()
    {

        $proxmoxClientServiceMock = $this->createMock(ProxmoxClientService::class);
        $loggerMock = $this->createMock(LoggerInterface::class);

        $getVersionServiceMock = $this->getMockBuilder(IGetVersionService::class)
                                       ->getMock();

        $node_name = 'ns1000';
        $getVersionServiceMock->expects($this->once())
                                                    ->method('getVersion')
                                                    ->with($node_name)
                                                    ->willReturn($this->versionData);
                               
        $getVersionService = new GetVersionService($proxmoxClientServiceMock, $loggerMock);
        $this->assertInstanceOf(IGetVersionService::class, $getVersionService);
                               
        $resultGetVersion = $getVersionServiceMock->getVersion($node_name);
        $this->assertInstanceOf(VersionResponse::class, $resultGetVersion);           
        $this->assertIsArray($resultGetVersion->toArray());

        $expectedArray = [
            "release" => "8.1",
            "repoid" => "ec5affc9e41f1d79",
            "version" => "8.1.4"
        ];
        $this->assertEquals($expectedArray, $resultGetVersion->toArray());
        
        
    }
    
}