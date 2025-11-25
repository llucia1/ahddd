<?php
declare(strict_types=1);

namespace Common\Infrastructure\DB;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

class DatabaseTestCase extends KernelTestCase
{
    protected EntityManagerInterface $entityManager;

    /**
     * @throws ToolsException
     */
    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        if ('test' !== $kernel->getEnvironment()) {
            throw new LogicException('Execution only in Test environment possible!');
        }

        $this->initDatabase($kernel);

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * @throws ToolsException
     */
    private function initDatabase(KernelInterface $kernel): void
    {
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();

        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }
}