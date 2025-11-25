<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250627ChangeUuidFieldsInIp4 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change UUID fields in ip4 table to remove unique constraint';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_IP_UUID ON ip4'); 
    }
}
