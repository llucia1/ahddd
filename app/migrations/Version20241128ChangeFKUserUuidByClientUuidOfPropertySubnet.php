<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241128ChangeFKUserUuidByClientUuidOfPropertySubnet extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();

        $columns = $schemaManager->listTableColumns('property_ip4_subnet');
        if (!array_key_exists('uuid', $columns)) {
            $this->addSql('ALTER TABLE property_ip4_subnet ADD uuid VARCHAR(36) NOT NULL');
        }

        $this->addSql('ALTER TABLE property_ip4_subnet DROP FOREIGN KEY FK_USER_PROPERTY_IP4SUBNET');
        $this->addSql('ALTER TABLE property_ip4_subnet DROP COLUMN user_id');
        $this->addSql('ALTER TABLE property_ip4_subnet ADD client_uuid VARCHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE property_ip4_subnet ADD CONSTRAINT FK_property_ip4_subnet_client_uuid FOREIGN KEY (client_uuid) REFERENCES clients (uuid)');

    }
}
