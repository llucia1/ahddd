<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;





/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241209CreateConfigurationCommunicationsTable extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }


    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE IF NOT EXISTS configuration_communications (
            id BIGINT AUTO_INCREMENT NOT NULL,
            type VARCHAR(255) NOT NULL,
            protocol VARCHAR(255) NULL,
            host VARCHAR(255) NULL,
            username VARCHAR(255) NULL,
            password VARCHAR(255) NULL,
            port INT NULL,
            active BOOL NOT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME DEFAULT NULL,
            PRIMARY KEY(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
    }
}
