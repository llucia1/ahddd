<?php
declare(strict_types=1);

namespace Proxmox\Common\Keyboard\Application\Service;


use GridCP\Proxmox\Common\Application\Vga\Service\GetVgaService;
use GridCP\Proxmox\Common\Domain\Vga\Repository\IVgaService;

use PHPUnit\Framework\TestCase;

class GetVgaTest extends TestCase
{
    protected IVgaService $vgaRepository;
    protected string $filter;
    protected GetVgaService $getVgaService;
    public function setUp(): void
    {
        $this->vgaRepository  = $this->getMockBuilder(IVgaService::class)
                                     ->getMock();
        $this->getVgaService = new GetVgaService($this->vgaRepository);

        $this->filter = 'w';
    }
    
    public function testGetAllOK():void
    {
        $results = [
            "cirrus",
            "qxl",
            "qxl2",
            "qxl3",
            "qxl4",
            "none",
            "serial0",
            "serial1",
            "serial2",
            "serial3",
            "std",
            "virtio",
            "virtio-gl",
            "vmwar",
            "vmware"
          ];
        $this->vgaRepository ->expects($this->once())
        ->method('gets')
        ->willReturn($results);
        $resultService = $this->getVgaService->__invoke();
        $this->assertIsArray($resultService);
        $this->assertCount(15, $resultService);
    }
    
    public function testGetAllWithFilter():void
    {
        $results = [  
                            "vmwar",
                            "vmware"
                        ];
                        
        $this->vgaRepository ->expects($this->once())
                        ->method('filter')
                        ->with($this->filter)
                        ->willReturn($results);
        $resultService = $this->getVgaService->__invoke($this->filter);

        $this->assertIsArray($resultService);
        $this->assertCount(2, $resultService);
        $this->assertCount(count($results), $resultService);
        
        foreach ($resultService as $kb) {
            $this->assertContains($kb, $results, "The Vga '$kb' is not expected in the result.");
        }
    } 
    /* 
    php bin/phpunit tests/Proxmox/Common/Vga/Application/Service/GetVgaTest.php
    */
}