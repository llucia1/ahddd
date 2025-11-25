<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250122CreateTableForEmailControl extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('   CREATE TABLE email_templates 
            (
                id BIGINT AUTO_INCREMENT PRIMARY KEY,
                uuid VARCHAR(36) NOT NULL,
                language varchar(3) NOT NULL,
                name VARCHAR(255), 
                subject VARCHAR(255), 
                body TEXT NOT NULL, 
                active BOOL NOT NULL, 
                version INTEGER default 0 ,
                created_at DATETIME NOT NULL,
                updated_at DATETIME DEFAULT NULL
            )ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS email_templates;');
    }
}
