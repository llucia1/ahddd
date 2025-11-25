<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241217DropForegingKeyClientUuidPropertySubnet extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $connection = $this->connection;
        $query = "
            SELECT COUNT(*) AS cnt
            FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_NAME = 'property_ip4_subnet'
              AND TABLE_SCHEMA = DATABASE()
              AND CONSTRAINT_NAME = 'FK_property_ip4_subnet_client_uuid'
        ";

        $result = $connection->fetchOne($query);

        if ($result > 0) {
            $this->addSql('ALTER TABLE property_ip4_subnet DROP FOREIGN KEY FK_property_ip4_subnet_client_uuid');
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql("
            ALTER TABLE property_ip4_subnet 
            ADD CONSTRAINT FK_property_ip4_subnet_client_uuid 
            FOREIGN KEY (client_uuid) REFERENCES clients (uuid);
        ");
    }
}
