<?php
declare(strict_types=1);

namespace Node\Application\Helpers;
use Faker\Factory as FakerFactory;

use GridCP\Node\Domain\VO\Cpu;
use GridCP\Node\Domain\VO\CpuCustom;
use GridCP\Node\Domain\VO\CpuName;
use GridCP\Node\Domain\VO\CpuVendor;
use GridCP\Node\Domain\VO\FloatgroupsUuids;
use GridCP\Node\Domain\VO\NodeDisplay;
use GridCP\Node\Domain\VO\NodeVPEIp;
use GridCP\Node\Domain\VO\NodeKeyboard;
use GridCP\Node\Domain\VO\NodeGCPName;
use GridCP\Node\Domain\VO\NodeNetworkInterface;
use GridCP\Node\Domain\VO\Node;
use GridCP\Node\Domain\VO\Noderiority;
use GridCP\Node\Domain\VO\NodeVPEHostName;
use GridCP\Node\Domain\VO\NodeVPEPassword;
use GridCP\Node\Domain\VO\NodeVPEPort;
use GridCP\Node\Domain\VO\NodeVPERealm;
use GridCP\Node\Domain\VO\NodeVPEUsername;
use GridCP\Node\Domain\VO\NodeSshPort;
use GridCP\Node\Domain\VO\NodeStorage;
use GridCP\Node\Domain\VO\NodeStorageBackUp;
use GridCP\Node\Domain\VO\NodeStorageImage;
use GridCP\Node\Domain\VO\NodeStorageIso;
use GridCP\Node\Domain\VO\NodeTimeZone;
use GridCP\Node\Domain\VO\NodeUuid;
use GridCP\Node\Domain\VO\NodeVPEName;
use GridCP\Common\Domain\ValueObjects\UuidValueObject;




trait NodeTestTrait
{
  
    public function createNode(): Node
    {
      $faker = FakerFactory::create();

      return new Node(
          new NodeUuid(UuidValueObject::random()->value()),
          new NodeGCPName($faker->name()),
          new NodeVPEName($faker->name()),
          new NodeVPEHostName($faker->name()),
          new NodeVPEUsername($faker->userName()),
          new NodeVPEPassword($faker->password()),
          new NodeVPERealm('pam'),
          new NodeVPEPort($faker->randomNumber(4)),
          new NodeVPEIp($faker->ipv4()),
          new NodeSshPort($faker->randomNumber(4)),
          new NodeTimeZone($faker->timezone()),
          new NodeKeyboard($faker->randomLetter()),
          new NodeDisplay($faker->text(20)),
          new NodeStorage($faker->text(20)),
          new NodeStorageIso($faker->text(20)),
          new NodeStorageImage($faker->text(20)),
          new NodeStorageBackUp($faker->text(20)),
          new NodeNetworkInterface($faker->text(20)),
          new Cpu(new CpuName("GenuineIntel"), new CpuVendor("Cascadelake-Server-v4"), new CpuCustom(0)),
          new Noderiority(1),
          new FloatgroupsUuids([UuidValueObject::random()->value(), UuidValueObject::random()->value()])
      );      
    }

    public function execRepositoryMethod($method, $params, $return)
    {
      return $method
      ->expects($this->once())
      ->method('findByUuid')
      ->with($params)
      ->willReturn($return);
    }

}