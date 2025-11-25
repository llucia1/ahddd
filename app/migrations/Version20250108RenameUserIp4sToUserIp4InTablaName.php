<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250108RenameUserIp4sToUserIp4InTablaName extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('RENAME TABLE user_ip4s TO user_ip4');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('RENAME TABLE user_ip4 TO user_ip4s');
    }
}
