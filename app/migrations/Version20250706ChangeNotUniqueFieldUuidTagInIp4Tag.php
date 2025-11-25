<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250706ChangeNotUniqueFieldUuidTagInIp4Tag extends AbstractMigration
{

    public function getDescription(): string
    {
        return 'Remove UNIQUE constraint from uuid and tag columns on ip4_tag';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE ip4_tag DROP INDEX UNIQ_TAG_UUID;');
        $this->addSql('ALTER TABLE ip4_tag DROP INDEX UNIQ_TAG_TAG;');
    }
}
