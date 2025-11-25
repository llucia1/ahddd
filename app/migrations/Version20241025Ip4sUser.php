<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241025Ip4sUser extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE IF NOT EXISTS user_ip4s (
            id BIGINT AUTO_INCREMENT NOT NULL PRIMARY KEY,
            ip VARCHAR(20) NOT NULL,
            user_id BIGINT NOT NULL,
            active BOOL NOT NULL DEFAULT TRUE,
            created_at DATETIME NOT NULL,
            updated_at DATETIME DEFAULT NULL,
            CONSTRAINT FK_USER_IP4S FOREIGN KEY (user_id) REFERENCES user(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

    }
}