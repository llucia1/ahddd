<?php
declare(strict_types=1);

namespace Proxmox\Networks\Application\Service;

use Faker\Generator;
use GridCP\Common\Domain\Bus\Query\QueryBus;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;



use GridCP\Proxmox\Networks\Application\Response\NetworkResponse;

use GridCP\Proxmox\Common\Infrastructure\ProxmoxClientService;
use GridCP\Proxmox\Networks\Application\Service\GetNetworksService;
use GridCP\Proxmox\Networks\Domain\Services\IGetNetworksService;

use Psr\Log\LoggerInterface;

class GetNetworksServiceTest extends TestCase
{
    protected Generator $faker;

    private $proxmoxClientServiceMock;
    private $loggerMock;
    private $queryBusMock;
    private $networksData;


    protected function setUp(): void
    {
        parent::setUp();
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->queryBusMock = $this->createMock(QueryBus::class);

        $this->proxmoxClientServiceMock = new ProxmoxClientService($this->loggerMock, $this->queryBusMock);




        $this->networksData = [
                            new NetworkResponse(
                                'manual',
                                '0',
                                true,
                                'vmbr0',
                                5,
                                'bridge0',
                                true,
                                'manual',
                                'off',
                                "24",
                                '188.213.4.30/24',
                                'ens2',
                                '188.213.4.1',
                                [
                                                    'inet'
                                                ],
                                "188.213.4.30"
                            ),
                            new NetworkResponse(
                                'manual',
                                '0',
                                true,
                                'vmbr1',
                                5,
                                'bridge1',
                                true,
                                'manual',
                                'off',
                                "16",
                                '188.218.8.38/24',
                                'ens2',
                                '188.213.8.1',
                                [
                                                    'inet'
                                                ],
                                "188.218.8.38"
                            ),
        ];

    }
    public static function login():void
    {
        $client = new Client(['base_uri'=>'http://localhost:80']);
        $result = $client->post('/api/v1/auth/login',['json'=>self::$USER_LOGIN]);
        self::$JWT =   json_decode($result->getBody()->getContents())->token;
    }

    
    public function testInvokeReturnsArrayOfNetworkResponses()
    {
        $proxmoxClientServiceMock = $this->createMock(ProxmoxClientService::class);
        $loggerMock = $this->createMock(LoggerInterface::class);

        $getNetworksServiceMock = $this->getMockBuilder(IGetNetworksService::class)
                                       ->getMock();

        $node_name = 'ns1000';
        $getNetworksServiceMock->expects($this->once())
                               ->method('getNetworks')
                               ->with($node_name)
                               ->willReturn($this->networksData);

        $getNetworksService = new GetNetworksService($proxmoxClientServiceMock, $loggerMock);
        $this->assertInstanceOf(IGetNetworksService::class, $getNetworksService);

        $result = $getNetworksServiceMock->getNetworks($node_name);

        $this->assertIsArray($result);

        foreach ($result as $network) {
            $this->assertInstanceOf(NetworkResponse::class, $network);
        }
    }
    
}