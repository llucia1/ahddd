<?php

namespace Fixtures;


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use GridCP\Proxmox\Os\Infrastructure\DB\MySQL\Entity\OsEntity;

class OsFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        
        $oses = [
            ['uuid' => '7a4039e8-3e7e-4f61-a2f5-9bdeab0a2df1', 'name' => 'Debian 12', 'tag' => 'debian12', 'image' => 'Debian-12-x86_64-GridCP-PVE_KVM-20241005.qcow2', 'username' => 'root'],
            ['uuid' => '2e9c7f0c-279f-4682-942c-7d0d0b9b48b3', 'name' => 'Alma 8', 'tag' => 'alma8', 'image' => 'AlmaLinux-8-x86_64-GridCP-PVE_KVM-20240920.qcow2', 'username' => 'root'],
            ['uuid' => 'c10336ce-8de4-4d29-a9c0-34320d51d956', 'name' => 'Alma 9', 'tag' => 'alma9', 'image' => 'AlmaLinux-9-x86_64-GridCP-PVE_KVM-20240920.qcow2', 'username' => 'root'],
            ['uuid' => 'e35f76b0-1e75-44ef-8747-cf0b08b35a3a', 'name' => 'Centos 7', 'tag' => 'centos7', 'image' => 'CentOS-7-x86_64-GridCP-PVE_KVM-20240920.qcow2', 'username' => 'root'],
            ['uuid' => 'b30cdcb5-ccb7-484a-a05c-8a37cbbdc7c0', 'name' => 'Debian 11', 'tag' => 'debian11', 'image' => 'Debian-11-x86_64-GridCP-PVE_KVM-20240920.qcow2', 'username' => 'root'],
            ['uuid' => '2423bbd5-ab56-4c54-8358-c2be930af44d', 'name' => 'Debian 10', 'tag' => 'debian10', 'image' => 'Debian-10-x86_64-GridCP-PVE_KVM-20240920.qcow2', 'username' => 'root'],
            ['uuid' => 'c076aba0-9854-4781-a993-b44a6a7acb47', 'name' => 'RouterOS 6', 'tag' => 'routeros6', 'image' => 'RouterOS-6-x86_64-GridCP-PVE_KVM-20240903.img', 'username' => 'admin'],
            ['uuid' => '85b4e669-388e-4975-8597-b4d02c452905', 'name' => 'RouterOS 7', 'tag' => 'routeros7', 'image' => 'RouterOS-7-x86_64-GridCP-PVE_KVM-20240903.img', 'username' => 'admin'],
            ['uuid' => '46f97326-f29f-4f6f-a57b-adb2644a31ce', 'name' => 'Ubuntu 16', 'tag' => 'ubuntu16', 'image' => 'Ubuntu-16-x86_64-GridCP-PVE_KVM-20240920.img', 'username' => 'root'],
            ['uuid' => 'aa23bca2-c890-43fd-b3a5-545dce001431', 'name' => 'Ubuntu 18', 'tag' => 'ubuntu18', 'image' => 'Ubuntu-18-x86_64-GridCP-PVE_KVM-20240920.img', 'username' => 'root'],
            ['uuid' => '611d482e-aac7-4d04-9fac-09084d1c4464', 'name' => 'Ubuntu 20', 'tag' => 'ubuntu20', 'image' => 'Ubuntu-20-x86_64-GridCP-PVE_KVM-20240920.img', 'username' => 'root'],
            ['uuid' => '9f71f3f3-c563-44ea-adbb-2c84e49099fb', 'name' => 'Ubuntu 22', 'tag' => 'ubuntu22', 'image' => 'Ubuntu-22-x86_64-GridCP-PVE_KVM-20240920.img', 'username' => 'root'],
            ['uuid' => 'a52e17a9-5236-431b-b766-a9fb381d5f5d', 'name' => 'Ubuntu 24', 'tag' => 'ubuntu24', 'image' => 'Ubuntu-24-x86_64-GridCP-PVE_KVM-20240920.img', 'username' => 'root'],
            ['uuid' => 'e730c678-7a6a-47ee-b9c7-25b242bd9ee9', 'name' => 'Windows 11', 'tag' => 'windows11', 'image' => 'Windows-11-Enterprise-x86_64-GridCP-PVE_KVM-20240618.qcow2', 'username' => null],
        ];

        foreach ($oses as $data) {
            $os = new OsEntity();
            $os->setUuid($data['uuid']);
            $os->setName($data['name']);
            $os->setTag($data['tag']);
            $os->setImage($data['image']);
            $os->setUsername($data['username']);
            $os->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($os);
        }

        $manager->flush();


    }
}