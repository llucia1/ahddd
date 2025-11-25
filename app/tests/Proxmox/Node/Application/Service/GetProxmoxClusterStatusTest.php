<?php
declare(strict_types=1);

namespace Proxmox\Node\Application\Service;


use Faker\Factory as FakerFactory;
use Faker\Generator;
use GridCP\Proxmox_Client\Cluster\Domain\Exceptions\ClusterNotFound;
use GridCP\Common\Domain\Bus\Query\QueryBus;
use GridCP\Proxmox\Common\Infrastructure\ProxmoxClientService;
use GridCP\Proxmox\Node\Application\Service\GetProxmoxClusterStatusService;
use GridCP\Proxmox\Node\Domain\Responses\ClusterResponse;
use GridCP\Proxmox\Node\Domain\Responses\NodesCluster;
use GridCP\Proxmox\Node\Domain\Responses\NodesClusterResponse;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use GridCP\Proxmox_Client\Cluster\Domain\Responses\ClusterResponse as ClusterResponseApi;
use GridCP\Proxmox_Client\Cluster\Domain\Responses\NodesClusterResponse as NodesClusterResponseApi;
use GridCP\Proxmox_Client\Cluster\Domain\Responses\NodesCluster as NodesClusterApi;
use GridCP\Proxmox\Node\Domain\Exception\ClusterNotFoundInGridcp;

class GetProxmoxClusterStatusTest extends  TestCase
{

    protected ProxmoxClientService $proxmoxClientServiceMock;
    protected LoggerInterface $logger;
    protected QueryBus $queryBusMock;

    private GetProxmoxClusterStatusService $service;
    private Generator $faker;

    public function setUp(): void
    {
    

        $this->proxmoxClientServiceMock = $this->createMock(ProxmoxClientService::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->queryBusMock = $this->createMock(QueryBus::class);


        
        $this->faker = FakerFactory::create();

        $this->service = new GetProxmoxClusterStatusService(
            $this->logger,
            $this->queryBusMock,
            $this->proxmoxClientServiceMock
        );    
    }

    public function testGetClusterStatusSuccess(): void
    {
        $cluster1Api = new NodesClusterApi(
            "ns1050",
            "188.213.4.80",
            "node",
            "",
            false,
            0,
            1,
            "node/ns1050"
        );
        $cluster2Api = new NodesClusterApi(
            "ns1051",
            "188.213.4.81",
            "node",
            "",
            false,
            0,
            1,
            "node/ns1051"
        );
        $clusterResponseApi = new NodesClusterResponseApi(...[$cluster1Api, $cluster2Api]);
        $clustersResponseApi  = new ClusterResponseApi(
            "cluster",
            "cluster1",
            3,
            "cluster",
            $clusterResponseApi
        );

        $cluster1 = new NodesCluster(
            "ns1050",
            "188.213.4.80",
            "node",
            "",
            false,
            0,
            1,
            "node/ns1050"
        );
        $cluster2 = new NodesCluster(
            "ns1051",
            "188.213.4.81",
            "node",
            "",
            false,
            0,
            1,
            "node/ns1051"
        );
        $clustersResponse = new NodesClusterResponse(...[$cluster1, $cluster2]);

        $clusterResponse  = new ClusterResponse(
            "cluster",
            "cluster1",
            3,
            "cluster",
            $clustersResponse
        );
        $nodoName = 'ns1049';
        $this->proxmoxClientServiceMock->expects($this->any())
             ->method('getNodesWithAuth')
             ->with( $nodoName );

        $this->proxmoxClientServiceMock->expects($this->any())
                                   ->method('GetClusterStatus')
                                   ->willReturn($clustersResponseApi);

        $results  = $this->service->__invoke($nodoName);
        $this->assertInstanceOf(ClusterResponse::class, $results);

        $this->assertEquals($clusterResponse->getType(), $results->getType() );
        $this->assertEquals($clusterResponse->getName(), $results->getName() );
        $this->assertEquals($clusterResponse->getVersion(), $results->getVersion() );
        $this->assertEquals($clusterResponse->getId(), $results->getId() );
        $this->assertEquals($clusterResponse->getNodesCluster(), $results->getNodesCluster() );
        $this->assertEquals($clusterResponse, $results );

        $this->assertInstanceOf(NodesClusterResponse::class, $results->getNodesCluster() );
        

    }

    public function testGetClusterStatusNotFound(): void
    {
        $nodoName = 'ns1047';
        $this->proxmoxClientServiceMock->expects($this->any())
             ->method('getNodesWithAuth')
             ->with( $nodoName );

        $this->proxmoxClientServiceMock->expects($this->any())
                                   ->method('GetClusterStatus')
                                   ->willReturn(new ClusterNotFound());

        
        try {
            $this->service->__invoke($nodoName);
        } catch (ClusterNotFoundInGridcp $e) {
            $this->assertInstanceOf(ClusterNotFoundInGridcp::class, $e);
            return;
        }
    
        $this->fail('Se esperaba una excepción NodeNotExistError');
        
        $this->expectExceptionCode(404);        
        

    }
    
}
