<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250224AddPriorityFieldToNodeAndIp extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE node ADD priority INT NOT NULL DEFAULT 0');
        $this->addSql('ALTER TABLE ip4 ADD priority INT NOT NULL DEFAULT 0');
        $this->addSql('ALTER TABLE node ADD CONSTRAINT chk_node_priority CHECK (priority BETWEEN 0 AND 10)');
        $this->addSql('ALTER TABLE ip4 ADD CONSTRAINT chk_ip4_priority CHECK (priority BETWEEN 0 AND 10)');
    }

}
