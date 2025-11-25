<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250808CreateLogs extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Table Logs';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE logs (
                id INT AUTO_INCREMENT NOT NULL,
                uuid VARCHAR(36) NOT NULL,
                auth_id BIGINT NOT NULL,
                ip VARCHAR(45) DEFAULT NULL,
                date DATETIME NOT NULL,
                resource VARCHAR(255) DEFAULT NULL,
                method VARCHAR(10) DEFAULT NULL,
                url TEXT DEFAULT NULL,

                request JSON DEFAULT NULL,
                response JSON DEFAULT NULL,

                exception LONGTEXT DEFAULT NULL,
                active TINYINT(1) NOT NULL DEFAULT 1,
                created_at DATETIME NOT NULL,
                updated_at DATETIME DEFAULT NULL,
                UNIQUE INDEX UNIQ_LOGS_UUID (uuid),
                INDEX IDX_LOGS_AUTH_ID (auth_id),
                PRIMARY KEY(id),
                CONSTRAINT FK_LOGS_AUTH FOREIGN KEY (auth_id) REFERENCES auth (id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
        ');
    }
}
