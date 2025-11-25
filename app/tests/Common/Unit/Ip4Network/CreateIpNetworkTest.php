<?php
declare(strict_types=1);

namespace Tests\Common\Unit\Ip4Network;

use PHPUnit\Framework\TestCase;

class CreateIpNetworkTest
{
    private $ip4NetworkRepository;

    protected function setUp(): void
    {
        $this->ip4NetworkRepository = $this->createMock(IIp4NetworkRepository::class);
    }

    /* public function testCreateIpNetworkSuccessfully()
     {

         $this->assertNotEmpty($uuid, 'UUID is empty');
     }*/
}
