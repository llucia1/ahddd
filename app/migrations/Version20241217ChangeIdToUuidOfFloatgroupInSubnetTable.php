<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241217ChangeIdToUuidOfFloatgroupInSubnetTable extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE ip4_subnet DROP FOREIGN KEY FK_SUBNET_FLOATGROUP');

        $this->addSql('ALTER TABLE ip4_subnet ADD uuid_floatgroup CHAR(36) DEFAULT NULL');

        $this->addSql("
            UPDATE ip4_subnet AS subnet
            INNER JOIN ip4_float_group AS floatgroup
                ON subnet.id_floatgroup = floatgroup.id
            SET subnet.uuid_floatgroup = floatgroup.uuid
            WHERE subnet.id_floatgroup IS NOT NULL
        ");

        $this->addSql('ALTER TABLE ip4_subnet DROP COLUMN id_floatgroup');
    }


    public function down(Schema $schema): void
    {


        $this->addSql('ALTER TABLE ip4_subnet ADD id_floatgroup INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ip4_subnet DROP COLUMN uuid_floatgroup');


        $this->addSql('ALTER TABLE ip4_subnet ADD CONSTRAINT FK_SUBNET_FLOATGROUP FOREIGN KEY (id_floatgroup) REFERENCES ip4_float_group (id)');
    }
}
