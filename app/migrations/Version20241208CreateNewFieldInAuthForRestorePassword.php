<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;





/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241208CreateNewFieldInAuthForRestorePassword extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }


    public function up(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();

        $columns = $schemaManager->listTableColumns('auth');
        if (!array_key_exists('restore_password_key', $columns)) {
            $this->addSql('ALTER TABLE auth ADD restore_password_key VARCHAR(54)  NULL');
        }
    }
}
