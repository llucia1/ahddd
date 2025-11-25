<?php
declare(strict_types=1);

namespace Proxmox\Storage\Application\Service;

use Faker\Factory as FakerFactory;
use GridCP\Proxmox_Client\Commons\Domain\Exceptions\AuthFailedException;
use GridCP\Proxmox_Client\Storages\Domain\Exceptions\StoragesNotFound;
use GridCP\Proxmox\Common\Infrastructure\ProxmoxClientService;
use GridCP\Proxmox\Storage\Application\Service\GetProxmoxStorageService;
use GridCP\Proxmox\Storage\Domain\Service\IGetProxmoxStorageService;
use GridCP\Proxmox\Storage\Domain\Vo\GcpNodeNameVo;
use GridCP\Proxmox\Storage\Presentation\Rest\V1\GetProxmoxStorage;
use GridCP\Proxmox\Storage\Domain\Exception\GetProxmoxStorageServiceException;
use GridCP\Proxmox\Storage\Domain\Exception\NodeStorageNotFoundException;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use GridCP\Common\Domain\Bus\Query\QueryBus;
use GridCP\Node\Application\Response\NodeResponse;
use GridCP\Node\Domain\Exception\NodeNotExistError;
use GridCP\Proxmox_Client\Commons\Domain\Exceptions\HostUnreachableException;
use GridCP\Proxmox_Client\Storages\Domain\Responses\StoragesResponse;
use GridCP\Proxmox\Storage\Domain\Exception\HostUnreachableException as UnreachableException;
use GridCP\Proxmox\Storage\Domain\Exception\NodeNotExistException;
use GridCP\Proxmox\Storage\Domain\Exception\NodeStorageUnathorizedexception;

class GetProxmoxStorageTest extends TestCase
{
    private ProxmoxClientService $proxmoxStorageService;
    private GetProxmoxStorageService $storageService;
    private QueryBus $queryBusMock;
    private GetProxmoxStorage $getProxmoxStorage;
    private GcpNodeNameVo $node_name;
    private NodeResponse $node;
    private string $nodeName = 'ns1048';


    protected function setUp(): void
    {
        
        $logger = $this->createMock(LoggerInterface::class);
        $this->proxmoxStorageService = $this->getMockBuilder( ProxmoxClientService::class )->disableOriginalConstructor()->getMock();
        $this->queryBusMock = $this->createMock(QueryBus::class);
        $this->storageService = new GetProxmoxStorageService($logger, $this->queryBusMock,$this->proxmoxStorageService);
        $this->node_name = new GcpNodeNameVo('ns1048');

 
        $this->node = new NodeResponse(
            'f801ef6d-498c-44d8-a831-a25471ff593d',
            'individual-2',
            $this->nodeName,
            'https://ns1047.giner.network',
            'root',
            'GridCP',
            'pam',
            8006,
            '188.213.4.78', // NOSONAR
            22,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
        );
    }

    // php bin/phpunit tests/Proxmox/Storage/Application/Service/GetProxmoxStorageTest.php

    public function testGetStorageNodeNotFoundException(): void
    {

        $nodeNotFount = new NodeNotExistError();
        $this->queryBusMock ->expects($this->once())
                            ->method('ask')
                            ->willReturn($nodeNotFount);


        $this->expectException(NodeNotExistException::class);

        $this->storageService->__invoke(new GcpNodeNameVo(''),null);
    }

    public function testGetProxmoxStorageNotFounfServiceException(): void
    {

        $this->queryBusMock ->expects($this->once())
                            ->method('ask')
                            ->willReturn($this->node);
        $this->proxmoxStorageService ->expects($this->once())
                            ->method('__invoke');
        $this->proxmoxStorageService ->expects($this->once())
                                                ->method('getStorages')
                                                ->with($this->nodeName)
                                                ->willReturn(new StoragesNotFound());

        $this->expectException(NodeStorageNotFoundException::class);

        $this->storageService->__invoke(new GcpNodeNameVo(''),null);



    }

    public function testGetProxmoxStorageAuthFailfServiceException(): void
    {

        $this->queryBusMock ->expects($this->once())
                            ->method('ask')
                            ->willReturn($this->node);
        $this->proxmoxStorageService ->expects($this->once())
                            ->method('__invoke');
        $this->proxmoxStorageService ->expects($this->once())
                                                ->method('getStorages')
                                                ->with($this->nodeName)
                                                ->willReturn(new AuthFailedException());

        $this->expectException(NodeStorageUnathorizedexception::class);

        $this->storageService->__invoke(new GcpNodeNameVo(''),null);



    }

    public function testGetProxmoxStorageUnreachablefServiceException(): void
    {

        $this->queryBusMock ->expects($this->once())
                            ->method('ask')
                            ->willReturn($this->node);
        $this->proxmoxStorageService ->expects($this->once())
                            ->method('__invoke');
        $this->proxmoxStorageService ->expects($this->once())
                                                ->method('getStorages')
                                                ->with($this->nodeName)
                                                ->willReturn(new HostUnreachableException());

        $this->expectException(UnreachableException::class);

        $this->storageService->__invoke(new GcpNodeNameVo(''),null);



    }

    public function testGetProxmoxStorageServiceOkException(): void
    {

        $this->queryBusMock ->expects($this->once())
                            ->method('ask')
                            ->willReturn($this->node);
        $this->proxmoxStorageService ->expects($this->once())
                            ->method('__invoke');

        $result = new StoragesResponse();
        $this->proxmoxStorageService ->expects($this->once())
                                                ->method('getStorages')
                                                ->with($this->nodeName)
                                                ->willReturn($result);


        $this->storageService->__invoke(new GcpNodeNameVo(''),null);
    }




}
