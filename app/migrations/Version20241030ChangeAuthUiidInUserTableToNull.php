<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241030ChangeAuthUiidInUserTableToNull extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user MODIFY auth_uuid VARCHAR(36) NULL');
    }
    
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user MODIFY auth_uuid VARCHAR(36) NOT NULL');
    }
}
