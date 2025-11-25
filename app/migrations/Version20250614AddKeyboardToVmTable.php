<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250614AddKeyboardToVmTable extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add keyboard column to vm table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE vm ADD keyboard VARCHAR(5) DEFAULT NULL');
    }
}
