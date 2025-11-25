<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241023CreatePlanTable extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE IF NOT EXISTS plan (
            id BIGINT AUTO_INCREMENT NOT NULL,
            uuid VARCHAR(36) NOT NULL,
            name VARCHAR(180) NOT NULL,
            disk_size INT NOT NULL,
            cores INT NOT NULL,
            memory INT NOT NULL,
            traffic_limit INT NOT NULL,
            active BOOL NOT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME DEFAULT NULL,
            PRIMARY KEY(id),
            UNIQUE INDEX idx_uuid (uuid)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
    }
}
