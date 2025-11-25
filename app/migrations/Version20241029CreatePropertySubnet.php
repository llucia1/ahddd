<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241029CreatePropertySubnet extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE IF NOT EXISTS property_ip4_subnet (
            id BIGINT AUTO_INCREMENT PRIMARY KEY,
            subnet_id BIGINT NOT NULL,
            user_id BIGINT NULL,
            active BOOL NOT NULL DEFAULT TRUE,
            created_at DATETIME NOT NULL,
            updated_at DATETIME DEFAULT NULL,
            CONSTRAINT FK_SUBNET_PROPERTY_IP4SUBNET FOREIGN KEY (subnet_id) REFERENCES ip4_subnet(id),
            CONSTRAINT FK_USER_PROPERTY_IP4SUBNET FOREIGN KEY (user_id) REFERENCES user(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
    }

    public function down(Schema $schema): void
    {
        if ($schema->hasTable('property_ip4_subnet')) {
            $schema->dropTable('property_ip4_subnet');
        }
    }
}
