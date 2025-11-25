<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241018CreateTables extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE IF NOT EXISTS ip4_network_float_gorup (
            id BIGINT AUTO_INCREMENT NOT NULL,
            network_id BIGINT NOT NULL,
            floatgroup_id BIGINT NOT NULL,
            active TINYINT(1) DEFAULT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME DEFAULT NULL,
            PRIMARY KEY(id),
            INDEX idx_idnetwork (network_id),
            INDEX idx_idfloatgroup (floatgroup_id),
            CONSTRAINT fk_id_network FOREIGN KEY (network_id) REFERENCES ip4_network (id) ON DELETE CASCADE,
            CONSTRAINT fk_id_floatgroup FOREIGN KEY (floatgroup_id) REFERENCES ip4_float_group (id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');         
    }
}
