<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250311CreateRefreshTokenTable extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE IF NOT EXISTS refresh_tokens (
            id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL,
            uuid varchar(50) NOT NULL,
            auth_user_uuid varchar(50) NOT NULL,
            token varchar(250) NOT NULL,
            active BOOLEAN NOT NULL DEFAULT TRUE,
            expire_at datetime NOT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME DEFAULT NULL,
            PRIMARY KEY(id)   
        ) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci');

    }
}
