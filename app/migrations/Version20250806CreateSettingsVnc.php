<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250806CreateSettingsVnc extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE settings_vnc (
                        id INT AUTO_INCREMENT NOT NULL,
                        name VARCHAR(50) NOT NULL,
                        uuid VARCHAR(36) NOT NULL,
                        ssh_host VARCHAR(255) NOT NULL,
                        ssh_user VARCHAR(255) NOT NULL,
                        ssh_pass VARCHAR(255) NOT NULL,
                        ssh_port INT NOT NULL DEFAULT 22,
                        path_sh VARCHAR(255) DEFAULT NULL,
                        script_name VARCHAR(255) DEFAULT NULL,
                        active TINYINT(1) NOT NULL DEFAULT 1,
                        created_at DATETIME NOT NULL,
                        updated_at DATETIME NOT NULL,
                        UNIQUE INDEX UNIQ_SETTINGS_VNC_NAME (name),
                        PRIMARY KEY(id)
                    ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
                ');
    }
}
