<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241116CreateClientAndRelationTable extends AbstractMigration
{

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE IF NOT EXISTS clients (
            id BIGINT AUTO_INCREMENT PRIMARY KEY,
            uuid VARCHAR(36) NOT NULL,
            name varchar(255) NOT NULL,
            active BOOL NOT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME DEFAULT NULL,
             UNIQUE INDEX idx_uuid (uuid)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->addSql('CREATE TABLE IF NOT EXISTS auth_clients (
            id BIGINT AUTO_INCREMENT PRIMARY KEY,
            client_uuid varchar(36) NOT NULL,
            auth_id bigInt NOT NULL,
            is_owner BOOL NOT NULL,
            active BOOL NOT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME DEFAULT NULL,
            CONSTRAINT FK_AUTH FOREIGN KEY (auth_id) REFERENCES auth (id),
            CONSTRAINT FK_CLIENT FOREIGN KEY (client_uuid) REFERENCES clients (uuid)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');




    }

    public function down(Schema $schema): void
    {
        if ($schema->hasTable('clients')) {
            $schema->dropTable('clients');
        }
        if ($schema->hasTable('clients_auth')) {
            $schema->dropTable('clients_auth');
        }

    }
}
