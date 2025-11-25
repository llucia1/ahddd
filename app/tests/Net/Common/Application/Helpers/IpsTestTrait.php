<?php
declare(strict_types=1);

namespace Tests\Net\Common\Application\Helpers;

use GridCP\Common\Domain\ValueObjects\Ip4TagTag;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4Entity;
use GridCP\Net\Common\Infrastructure\DB\MySQL\Entity\Ip4NetworkEntity;
use GridCP\Net\Ip4\Domain\VO\Ip4;
use GridCP\Net\Ip4\Domain\VO\Ip4Active;
use GridCP\Net\Ip4\Domain\VO\Ip4Ip;
use GridCP\Net\Ip4\Domain\VO\Ip4Priority;
use GridCP\Net\Ip4\Domain\VO\Ip4Uuid;
use GridCP\Net\Ip4\Domain\VO\Ip4UuidNetwork;
use IPCalc\IP;

trait IpsTestTrait
{
    public function ip4Vo(  $cidr, $uuidNetwork, $idNetwork, $ip4, $uuid, $priority, $tag ):Ip4
    {
        $ip4 = $ip4 . '/' . $cidr;

        $ip4UuidNetwork = new Ip4UuidNetwork($uuidNetwork);
        $ip4Ip = new Ip4Ip($ip4);
        $ip4Active = new Ip4Active(true);
        $ip4UUID = new Ip4Uuid($uuid);
        $priorityVo = new Ip4Priority($priority);
        $ip4Tag = new Ip4TagTag($tag);
        return new Ip4($ip4UUID, $ip4Ip, $ip4UuidNetwork, $ip4Active,$priorityVo, $ip4Tag);  
    }
    public function ip4Entity(Ip4 $ip4, $networkEntity): Ip4Entity
    {
            $ip4Entity = new Ip4Entity();
            $ip4Entity->setUuid($this->ip4->Uuid()->value() );
            $ip4Entity->setIp($this->ip4->Ip4()->value());
            $ip4Entity->setActive(true);
            $ip4Entity->setNetwork($networkEntity);

            return $ip4Entity;
    }

    public function ip4Entitys(Ip4 $ip4, Ip4NetworkEntity $networkEntity): array
    {
            $ipCal = new IP($ip4->Ip4()->value());
            $ip4s = $this->getIp4s($ipCal);

            $result = [];
            foreach ($ip4s as $ipx)
            {
                $ip4Entity = new Ip4Entity();
                $ip4Entity->setUuid($ip4->Uuid()->value() );
                $ip4Entity->setIp($ipx);
                $ip4Entity->setActive(true);
                $ip4Entity->setNetwork($networkEntity);

                $result[] = $ip4Entity;
            }

            return $result;
    }


    public function ip4NetworkEntity(Ip4 $ip4, $faker):Ip4NetworkEntity
    {
            $networkEntity = new Ip4NetworkEntity();
            $networkEntity->setUuid($ip4->UuidNetwork()->value() );
            $networkEntity->setName($faker->name());
            return $networkEntity;
    }






}