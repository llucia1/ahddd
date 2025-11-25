<?php

namespace Fixtures;


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use GridCP\Proxmox\Common\Infrastructure\Keyboard\DB\MySQL\Entity\Keyboard;

class KeyboardFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $keyboards = [
            'de', 'de-ch', 'da', 'en-gb', 'en-us', 'es', 'fi', 'fr', 'fr-be', 'fr-ca',
            'fr-ch', 'hu', 'is', 'it', 'ja', 'lt', 'mk', 'nl', 'no', 'pl',
            'pt', 'pt-br', 'sv', 'sl', 'tr'
        ];

        foreach ($keyboards as $layout) {
            $keyboard = new Keyboard();
            $keyboard->setKeyboardName($layout);
            $manager->persist($keyboard);
        }

        $manager->flush();        



    }
}