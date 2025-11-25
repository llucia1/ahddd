<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241102DeleteFieldsVmAndUserToSubnetTable extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $sm = $this->connection->createSchemaManager();
        $tableColumns = $sm->listTableColumns('ip4_subnet');


        $foreignKeys = $sm->listTableForeignKeys('ip4_subnet');
        foreach ($foreignKeys as $foreignKey) {
            $localColumns = $foreignKey->getLocalColumns();
            if (in_array('id_vm', $localColumns)) {
                $this->addSql('ALTER TABLE ip4_subnet DROP FOREIGN KEY ' . $foreignKey->getName());
            }
            if (in_array('id_user', $localColumns)) {
                $this->addSql('ALTER TABLE ip4_subnet DROP FOREIGN KEY ' . $foreignKey->getName());
            }
        }


        if (array_key_exists('id_vm', $tableColumns)) {
            $this->addSql('ALTER TABLE ip4_subnet DROP COLUMN id_vm');
        }

        if (array_key_exists('id_user', $tableColumns)) {
            $this->addSql('ALTER TABLE ip4_subnet DROP COLUMN id_user');
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE ip4_subnet ADD id_vm BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE ip4_subnet ADD id_user BIGINT DEFAULT NULL');
        
        $this->addSql('ALTER TABLE ip4_subnet ADD CONSTRAINT FK_SUBNET_VM FOREIGN KEY (id_vm) REFERENCES vm(id)');
        $this->addSql('ALTER TABLE ip4_subnet ADD CONSTRAINT FK_SUBNET_USER FOREIGN KEY (id_user) REFERENCES user(id)');
    }
}
