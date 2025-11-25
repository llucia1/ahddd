<?php
declare(strict_types=1);

namespace Tests\Net\Ip4Subnet\Application\Helper;

use GridCP\Net\Ip4Subnet\Domain\VO\Ip4SubnetVo;
use GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Entity\Ip4SubnetOwnerEntity;
use GridCP\Net\Ip4FloatGroup\Domain\VO\Ip4FloatGroups as Ip4FloatGroupsVo;

use Faker\Factory as FakerFactory;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4FloatGroupEntity;
use GridCP\Net\Ip4Subnet\Infrastructure\DB\MySQL\Entity\Ip4SubnetEntity;
use GridCP\User\Infrastructure\DB\MySQL\Entity\UserEntity;

trait SubnetTools
{
    private function createPropertySubnetEntity(Ip4SubnetEntity $subnetEntity, UserEntity $userEntity, bool $active = true): Ip4SubnetOwnerEntity
    {
        $propertySubnetEntity = new Ip4SubnetOwnerEntity();
        $propertySubnetEntity->setSubnet( $subnetEntity );
        $propertySubnetEntity->setUser( $userEntity );
        $propertySubnetEntity->setActive($active );
        return $propertySubnetEntity;
    }
    private function createUserEntity(string $uuid, string $name, string $email): UserEntity
    {
        $userEntity = new UserEntity();
        $userEntity->setUuid($uuid);
        $userEntity->setFirstName($name);
        $userEntity->setEmail($email);
        
        return $userEntity;
    }
    private function createSubnetEntity(Ip4SubnetVo $subnetVo, Ip4FloatGroupEntity $floatGRoupEntity, bool $active = true): Ip4SubnetEntity
    {
        $subnetEntity = new Ip4SubnetEntity();
        $subnetEntity->setUuid($subnetVo->subnetUUid()->value());
        $subnetEntity->setIp($subnetVo->subnetIP()->value() );
        $subnetEntity->setMask($subnetVo->subnetMask()->value() );
        $subnetEntity->setFloatgroup($floatGRoupEntity);
        $subnetEntity->setActive($active );


        $reflection = new \ReflectionClass($subnetEntity);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);// NOSONAR
        $property->setValue($subnetEntity, $this->faker->numberBetween(1, 100));// NOSONAR

        
        return $subnetEntity;
    }
    private function createFloatgroupEntity(Ip4FloatGroupsVo $floatGroupVo): Ip4FloatGroupEntity
    {
        $floatgroupEntity = new Ip4FloatGroupEntity();
        $floatgroupEntity->setUuid($floatGroupVo->uuid()->value());
        $floatgroupEntity->setName($floatGroupVo->name()->value() );
        $floatgroupEntity->setActive($floatGroupVo->active()->value() );
        return $floatgroupEntity;
    }
}
