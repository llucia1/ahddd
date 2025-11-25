<?php

namespace Fixtures;


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use GridCP\Proxmox\Common\Infrastructure\Keyboard\DB\MySQL\Entity\Keyboard;
use GridCP\Proxmox\Common\Infrastructure\Vga\DB\MySQL\Entity\Vga;
use GridCP\User\Infrastructure\DB\MySQL\Entity\CountryEntity;

class VgaFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $vgas = [
            'cirrus',
            'qxl',
            'qxl2',
            'qxl3',
            'qxl4',
            'none',
            'serial0',
            'serial1',
            'serial2',
            'serial3',
            'std',
            'virtio',
            'virtio-gl',
            'vmwar',
            'vmware'
        ];

        foreach ($vgas as $name) {
            $vga = new Vga();
            $vga->setVgaName($name);
            $manager->persist($vga);
        }

        $manager->flush();
    }
}