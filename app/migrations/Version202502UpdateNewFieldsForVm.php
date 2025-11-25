<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version202502UpdateNewFieldsForVm extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE vm DROP COLUMN cpu');

        $this->addSql('ALTER TABLE vm ADD client_uuid VARCHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE vm ADD ip VARCHAR(25) DEFAULT NULL');
        $this->addSql('ALTER TABLE vm ADD gateway VARCHAR(25) DEFAULT NULL');
        $this->addSql('ALTER TABLE vm ADD disk_size INT DEFAULT NULL');
        $this->addSql('ALTER TABLE vm ADD memory INT DEFAULT NULL');
        $this->addSql('ALTER TABLE vm ADD traffic_limit INT DEFAULT NULL');
        $this->addSql('ALTER TABLE vm ADD username VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE vm ADD password VARCHAR(255) DEFAULT NULL');

        $this->addSql('ALTER TABLE vm ADD CONSTRAINT fk_vm_client FOREIGN KEY (client_uuid) REFERENCES clients (uuid)');
        $this->addSql('CREATE INDEX idx_client_uuid ON vm (client_uuid)');        
    }
}
