<?php
// SonarQube Exclusion for the entire file
// sonar.ignore=true
declare(strict_types=1);

namespace Tests\Net\Ip4Subnet\Application\Service;

use GridCP\Net\Ip4Subnet\Application\Service\DeleteSubnetService;
use GridCP\Net\Ip4Subnet\Domain\Repository\IIp4SubnetOwnerRepository;
use GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Repository\Ip4SubnetRepository;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use GridCP\Net\Ip4Subnet\Domain\Exception\SubnetNoFound;
use GridCP\Net\Ip4Subnet\Domain\VO\SubnetUuid;
use GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Entity\Ip4SubnetEntity;
use GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Entity\Ip4SubnetOwnerEntity;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DeleteSubnetTest extends TestCase
{
    private Ip4SubnetRepository $ip4SubnetRepository;
    private IIp4SubnetOwnerRepository $propertySubnetRepository;
    private LoggerInterface $logger;
    private DeleteSubnetService $deleteSubnetService;
    private SubnetUuid $subnetUuid;

    public function setUp(): void
    {
        $this->ip4SubnetRepository = $this->createMock(Ip4SubnetRepository::class);
        $this->propertySubnetRepository = $this->createMock(IIp4SubnetOwnerRepository::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->deleteSubnetService = new DeleteSubnetService(
            $this->ip4SubnetRepository,
            $this->propertySubnetRepository,
            $this->logger
        );

        $this->subnetUuid = new SubnetUuid('ae901ebb-656f-44ff-b7d4-80bae65629c2');
    }

    /**
     * @dataProvider provideDeleteSubnetScenarios
     */
    public function testDeleteSubnet($subnetExists, $propertyExists, $expectedException, $exceptionMessage = null): void
    {
        $subnetEntity = $subnetExists ? $this->createMock(Ip4SubnetEntity::class) : null;
        if ($subnetEntity) {
            $subnetEntity->method('getUuid')->willReturn($this->subnetUuid->value());
            $subnetEntity->method('setActive')->with(false);
        }
    
        $propertySubnetEntity = $propertyExists ? $this->createMock(Ip4SubnetOwnerEntity::class) : null;
        if ($propertySubnetEntity) {
            $propertySubnetEntity->method('setActive')->with(false);
        }
    
        $this->ip4SubnetRepository->expects($this->once())
            ->method('findByUuid')
            ->with($this->subnetUuid->value())
            ->willReturn($subnetEntity);
    
        if ($subnetExists) {
            $this->ip4SubnetRepository->expects($this->once())
                ->method('save')
                ->with($subnetEntity);
    
            $this->propertySubnetRepository->expects($this->once())
                ->method('findBySubnetUuid')
                ->with($this->subnetUuid->value())
                ->willReturn($propertySubnetEntity);
        }
    
        if ($propertyExists) {
            $this->propertySubnetRepository->expects($this->once())
                ->method('save')
                ->will($expectedException ? $this->throwException(new \Exception('Database save error')) : $this->returnValue(null));
        }
    
        if ($expectedException) {
            $this->expectException($expectedException);
            if ($exceptionMessage) {
                $this->expectExceptionMessage($exceptionMessage);
            }
        }
    
        $this->deleteSubnetService->__invoke($this->subnetUuid);
    }
    

    public function provideDeleteSubnetScenarios(): array
    {
        return [
            'Success' => [true, true, null],
            'Subnet Not Found' => [false, false, SubnetNoFound::class],
            'Property Not Found' => [true, false, null],
            'Save Error' => [true, true, HttpException::class, 'General errorDatabase save error'],
        ];
    }
    //     php bin/phpunit tests/Net/Ip4Subnet/Application/Service/DeleteSubnetTest.php
}
