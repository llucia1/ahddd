<?php
declare(strict_types=1);

namespace Tests\Net\Ip4\Application\Service;

use GridCP\Net\Ip4Subnet\Application\Service\DeletePropertySubnetService;
use GridCP\Net\Ip4Subnet\Domain\Repository\IIp4SubnetOwnerRepository;
use GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Repository\Ip4SubnetRepository;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use GridCP\Net\Ip4Subnet\Domain\VO\SubnetUuid;
use GridCP\Net\Ip4Subnet\Domain\Exception\SubnetNoFound;
use GridCP\Net\Ip4Subnet\Domain\Exception\PropertySubnetNotFound;
use GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Entity\Ip4SubnetEntity;
use GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Entity\Ip4SubnetOwnerEntity;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;


class DeletePropertySubnetTest extends TestCase
{
    private Ip4SubnetRepository $ip4SubnetRepository;
    private IIp4SubnetOwnerRepository $propertySubnetRepository;
    private LoggerInterface $logger;
    private DeletePropertySubnetService $deletePropertySubnetService;
    private SubnetUuid $subnetUuid;

    public function setUp(): void
    {
        $this->ip4SubnetRepository = $this->createMock(Ip4SubnetRepository::class);
        $this->propertySubnetRepository = $this->createMock(IIp4SubnetOwnerRepository::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->deletePropertySubnetService = new DeletePropertySubnetService(
            $this->ip4SubnetRepository,
            $this->propertySubnetRepository,
            $this->logger
        );

        $this->subnetUuid = new SubnetUuid('ae901ebb-656f-44ff-b7d4-80bae65629c2');
    }

    /**
     * @dataProvider provideDeletePropertySubnetScenarios
     */
    public function testDeletePropertySubnet($subnetExists, $propertyExists, $expectedException, $exceptionMessage = null): void
    {
        $subnetEntity = $subnetExists ? $this->createConfiguredMock(Ip4SubnetEntity::class, ['getId' => 1]) : null;
        $propertySubnetEntity = $propertyExists ? $this->createMock(Ip4SubnetOwnerEntity::class) : null;

        $this->ip4SubnetRepository->expects($this->once())
            ->method('findByUuid')
            ->with($this->subnetUuid->value())
            ->willReturn($subnetEntity);

        if ($subnetExists) {
            $this->propertySubnetRepository->expects($this->once())
                ->method('findBySubnetId')
                ->with($subnetEntity->getId())
                ->willReturn($propertySubnetEntity);
        }

        if ($propertyExists) {
            $propertySubnetEntity->expects($this->once())
                ->method('setActive')
                ->with(false);// NOSONAR

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

        $this->deletePropertySubnetService->__invoke($this->subnetUuid);
    }

    public function provideDeletePropertySubnetScenarios(): array
    {
        return [
            'Success' => [true, true, null],
            'Subnet Not Found' => [false, false, SubnetNoFound::class],
            'Property Not Found' => [true, false, PropertySubnetNotFound::class],
            'Save Error' => [true, true, HttpException::class, 'General errorDatabase save error'],
        ];
    }
}
/*

php bin/phpunit tests/Net/Ip4/Application/Service/DeletePropertySubnetTest.php
*/