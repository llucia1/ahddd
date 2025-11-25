<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241129NewPropertySubnet extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS property_ip4_subnet');

        $this->addSql('CREATE TABLE IF NOT EXISTS property_ip4_subnet (
            id BIGINT AUTO_INCREMENT PRIMARY KEY,
            uuid VARCHAR(36) NOT NULL,
            subnet_id BIGINT NOT NULL,
            client_uuid VARCHAR(36) DEFAULT NULL,
            active BOOL NOT NULL DEFAULT TRUE,
            created_at DATETIME NOT NULL,
            updated_at DATETIME DEFAULT NULL,
            UNIQUE INDEX idx_property_subnet_uuid (uuid),
            CONSTRAINT FK_SUBNET_PROPERTY_IP4SUBNET FOREIGN KEY (subnet_id) REFERENCES ip4_subnet(id),
            CONSTRAINT FK_property_ip4_subnet_client_uuid FOREIGN KEY (client_uuid) REFERENCES clients(uuid)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
        
        $this->addSql('CREATE INDEX idx_client_uuid ON property_ip4_subnet (client_uuid)');
}
}
