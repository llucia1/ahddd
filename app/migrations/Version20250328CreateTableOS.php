<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250328CreateTableOS extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
        CREATE TABLE os (
            id INT AUTO_INCREMENT NOT NULL,
            uuid VARCHAR(36) NOT NULL,
            name VARCHAR(255) NOT NULL,
            tag VARCHAR(255) NOT NULL,
            image VARCHAR(255) NOT NULL,
            username VARCHAR(255) DEFAULT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME DEFAULT NULL,
            UNIQUE INDEX UNIQ_SO_UUID (uuid),
            UNIQUE INDEX UNIQ_OS_NAME (name),
            UNIQUE INDEX UNIQ_OS_TAG (tag),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
         
        
    }
}


