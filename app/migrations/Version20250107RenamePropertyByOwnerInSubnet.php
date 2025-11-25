<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250107RenamePropertyByOwnerInSubnet extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('RENAME TABLE property_ip4_subnet TO ip4_subnet_owner');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('RENAME TABLE ip4_subnet_owner TO property_ip4_subnet');
    }
}
