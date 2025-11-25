<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241028CreateVmIp4Table extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {

        $this->addSql('CREATE TABLE IF NOT EXISTS vm_ip4 (
            id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL,
            vm_id BIGINT NOT NULL,
            ip4_id BIGINT UNSIGNED NOT NULL,
            active BOOL NOT NULL DEFAULT TRUE,
            created_at DATETIME NOT NULL,
            updated_at DATETIME DEFAULT NULL,
            PRIMARY KEY(id),
            INDEX IDX_VM_IP4_VM (vm_id),
            INDEX IDX_VM_IP4_IP4 (ip4_id),
            CONSTRAINT FK_VM_IP4_VM FOREIGN KEY (vm_id) REFERENCES vm (id),
            CONSTRAINT FK_VM_IP4_IP4 FOREIGN KEY (ip4_id) REFERENCES ip4 (id)
        ) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS vm_ip4');
    }
}
