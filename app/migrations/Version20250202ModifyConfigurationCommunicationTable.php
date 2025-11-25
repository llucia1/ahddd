<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250202ModifyConfigurationCommunicationTable extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE configuration_communications ADD uuid VARCHAR(36) not null AFTER id');
        $this->addSql('ALTER TABLE configuration_communications ADD principal BOOLEAN not null AFTER port');
    }


}
