<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250331AddOsNameToNode extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE node ADD id_os INT DEFAULT NULL');
        $this->addSql('ALTER TABLE node ADD CONSTRAINT FK_NODE_OS FOREIGN KEY (id_os) REFERENCES os(id)');
    }
}
