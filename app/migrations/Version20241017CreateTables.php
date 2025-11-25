<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241017CreateTables extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE IF NOT EXISTS device (
            id BIGINT AUTO_INCREMENT NOT NULL,
            uuid VARCHAR(36) NOT NULL,
            ip VARCHAR(20) NOT NULL,
            device VARCHAR(255) DEFAULT NULL,
            country VARCHAR(3) DEFAULT NULL,
            location VARCHAR(255) DEFAULT NULL,
            active TINYINT(1) DEFAULT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME DEFAULT NULL,
            PRIMARY KEY(id),
            UNIQUE INDEX idx_uuid (uuid),
            INDEX idx_ip (ip)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');


        $this->addSql('CREATE TABLE IF NOT EXISTS devices_auth (
            id BIGINT AUTO_INCREMENT NOT NULL,
            device_id BIGINT NOT NULL,
            auth_id BIGINT NOT NULL,
            active TINYINT(1) DEFAULT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME DEFAULT NULL,
            PRIMARY KEY(id),
            INDEX idx_iddevice (device_id),
            INDEX idx_idauth (auth_id),
            CONSTRAINT fk_id_device FOREIGN KEY (device_id) REFERENCES device (id) ON DELETE CASCADE,
            CONSTRAINT fk_id_auth FOREIGN KEY (auth_id) REFERENCES auth (id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci'); 
    } 

}
