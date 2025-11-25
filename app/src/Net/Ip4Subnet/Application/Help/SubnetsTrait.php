<?php
namespace GridCP\Net\Ip4Subnet\Application\Help;

use GridCP\Net\Ip4Subnet\Application\Response\FloatgroupResponse;
use GridCP\Net\Ip4Subnet\Application\Response\Ip4SubnetResponse;
use GridCP\Net\Ip4Subnet\Application\Response\Ip4SubnetsResponses;
use GridCP\Net\Ip4Subnet\Application\Response\OwnerResponse;
use GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Entity\Ip4SubnetEntity;
use GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Entity\Ip4SubnetOwnerEntity;

use function Lambdish\Phunctional\map;
trait SubnetsTrait
{

    public function subnetsClientResponses(array $allSubnets): Ip4SubnetsResponses
    {
        $subnetsResponse = array_map(
            fn($subnet) => new Ip4SubnetResponse(
                $subnet['subnetUuid'],
                $subnet['subnetIp'],
                $subnet['subnetMask'],
                $subnet['floatGroupId'] ? new FloatgroupResponse(
                    $subnet['floatGroupUuid'] ,
                    $subnet['floatGroupName']
                ) : null,
                $subnet['clientId'] ? new OwnerResponse(
                    $subnet['clientUuid'],
                    $subnet['clientName']
                ) : null
            ),
            $allSubnets
        );
        return new Ip4SubnetsResponses(...$subnetsResponse);
    }
    public function subnetsResponse(array $allSubnets): array
    {
        return map(
                fn(Ip4SubnetResponse $subnet): array => [
                    'uuid' => $subnet->uuid(),
                    'ip'=> $subnet->ip(),
                    'mask'=> $subnet->mask(),
                    "floatgroup" => $subnet->floatgroup() ? [
                                        'uuid' => $subnet->floatgroup()->uuid(),
                                        'name' => $subnet->floatgroup()->name()
                                    ] : null,
                    "owner" => $subnet->owner() ? [
                                'uuid' => $subnet->owner()->uuid(),
                                'clientUuid' => $subnet->owner()->name()
                            ]: null
                ],
                $allSubnets);
    }
    public function subnetResponse(Ip4SubnetEntity $subnetEntity): array
    {
        $owner = $this->getProperty($subnetEntity->getPropertySubnet());
        return [
                    'uuid' => $subnetEntity->getUuid(),
                    'ip'=> $subnetEntity->getIp(),
                    'mask'=> $subnetEntity->getMask(),
                    "floatgroup" => $subnetEntity->getUuidFloatgroup(),
                    "owner" => ($owner)? [
                                    "uuid" => $owner->getUuid(),
                                    "uuidClient" =>  $owner->getClientUuid()
                    ] : null
                ];
    }
    public function subnetsArrayResponse(array $subnet): array
    {
        return [
                    'uuid' => $subnet['uuid'],
                    'ip'=> $subnet['ip'],
                    'mask'=> $subnet['mask'],
                    "floatgroupUuid" => $subnet['floatgroup_uuid'] ? $subnet['floatgroup_uuid'] : null,
                                    "floatgroupName" => $subnet['floatgroup_name']? $subnet['floatgroup_name'] : null,
                    "owner" => $subnet['client_uuid'] ? [
                                'uuid' => $subnet['client_uuid'] ,
                                'clientUuid' => $subnet['client_name']
                            ]: null
                ];
    }

    private function getProperty(mixed $propertySubnets): ?Ip4SubnetOwnerEntity
    {
        $result = null;
        foreach ($propertySubnets as $propertySubnet) {
            if ($propertySubnet->isActive()) {
                $this->logger->info("Found Property Subnet For Delete With UUID: " . $propertySubnet->getUuid());
                $result = $propertySubnet;
            }
        }
        return $result;
    }



}