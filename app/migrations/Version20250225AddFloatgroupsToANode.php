<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250225AddFloatgroupsToANode extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE IF NOT EXISTS node_floatgroup (
            id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL,
            node_id BIGINT NOT NULL,
            floatgroup_id BIGINT NOT NULL,
            active BOOL NOT NULL DEFAULT TRUE,
            created_at DATETIME NOT NULL,
            updated_at DATETIME DEFAULT NULL,
            PRIMARY KEY(id),
            INDEX IDX_NODE_FLOATGROUP (node_id),
            INDEX IDX_FLOATGROUP_NODE (floatgroup_id),
            CONSTRAINT FK_NODE_FLOATGROUP FOREIGN KEY (node_id) REFERENCES node (id),
            CONSTRAINT FK_FLOATGROUP_NODE FOREIGN KEY (floatgroup_id) REFERENCES ip4_float_group (id)
        ) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci');

    }
}
