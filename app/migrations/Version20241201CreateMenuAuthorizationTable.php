<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;





/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241201CreateMenuAuthorizationTable extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }


    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE IF NOT EXISTS menu_authorization (
            id BIGINT AUTO_INCREMENT NOT NULL,
            uuid VARCHAR(36) NOT NULL,
            menu VARCHAR(255) NOT NULL,
            role VARCHAR(255) NOT NULL,
            user_uuid VARCHAR(36) NULL,
            client_uuid VARCHAR(36) NULL,
            active BOOL NOT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME DEFAULT NULL,
            PRIMARY KEY(id),
            UNIQUE INDEX idx_uuid (uuid)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
    }
}
