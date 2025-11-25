<?php
declare(strict_types=1);
namespace GridCP\Net\Ip4Subnet\Common\Service;


use GridCP\Net\Ip4Subnet\Application\Service\GetIp4SubnetService;
use GridCP\Net\Ip4Subnet\Domain\VO\Ip4SubnetPacthVo;
use Psr\Log\LoggerInterface;

use Doctrine\ORM\EntityManagerInterface;
use GridCP\Net\Ip4Subnet\Application\Service\AddPropertySubnet;
use GridCP\Net\Ip4Subnet\Application\Service\CreateIp4Subnet;
use GridCP\Net\Ip4Subnet\Application\Service\DeleteSubnetService;
use GridCP\Net\Ip4Subnet\Domain\VO\Ip4SubnetVo;
use GridCP\Net\Ip4Subnet\Domain\VO\SubnetMask;
use GridCP\Net\Ip4Subnet\Domain\VO\SubnetUuid;
use GridCP\Net\Ip4Subnet\Domain\VO\UuidClient;
use GridCP\Net\Ip4Subnet\Domain\VO\UuidFloatgroup;

final class PatchSubnetService
{
    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly GetIp4SubnetService $getIp4SubnetService, private readonly CreateIp4Subnet $addSubnetService, private readonly DeleteSubnetService $deleteSubnetService,  private readonly AddPropertySubnet $addPropertySubnetService, private readonly LoggerInterface $logger)// NOSONAR
    {}
    public function __invoke(Ip4SubnetPacthVo $subnetVo, bool $isAdmin): void
    {
        $this->entityManager->beginTransaction();
        try {

            $this->logger->info('Start Delete Subnet with: ' . $subnetVo->subnetUUid()->value());
            $subnetPacth = $this->getIp4SubnetService->__invoke( $subnetVo->subnetUUid() );
            $subnetPacth = $subnetPacth->get();
            if ($isAdmin) {
                $subnetPacth['mask'] = (is_null($subnetVo->subnetMask()))? $subnetPacth['mask'] : $subnetVo->subnetMask()->getValue();
                $subnetPacth['uuid_floatgroup'] = (is_null($subnetVo->subnetUUidFloatgroup()))?  $subnetPacth['uuid_floatgroup'] :  $subnetVo->subnetUUidFloatgroup()->value();
            }
            
            $this->deleteSubnetService->__invoke($subnetVo->subnetUUid());

            $this->logger->info('Start new Edited Subnet with: ' . $subnetVo->subnetUUid()->value());

            $newSubnetUuid = $this->addSubnetService->__invoke($this->dto($subnetVo, $subnetPacth) );

            $this->addPropertySubnetService->__invoke(
                                                        new SubnetUuid($newSubnetUuid),
                                                        ($subnetPacth['client_uuid'])? new UuidClient($subnetPacth['client_uuid']) : null
                                                    );// NOSONAR

            $this->entityManager->commit();

        } catch (\Exception $e) {
            $this->entityManager->rollback();
            throw $e;
        }
    }
    private function dto(Ip4SubnetPacthVo $subnetVo, array $subnetPacth):Ip4SubnetVo
    {
        return new Ip4SubnetVo(
            new SubnetUuid( $subnetPacth['uuid'] ),
            new UuidFloatgroup($subnetPacth['uuid_floatgroup']),
            new SubnetMask($subnetPacth['mask']),
            (is_null($subnetVo->subnetIP()) ? null: $subnetVo->subnetIP())
        );
    }


}
