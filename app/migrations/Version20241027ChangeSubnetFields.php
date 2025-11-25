<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241027ChangeSubnetFields extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {

        // Eliminar las restricciones de clave foránea
        $this->addSql('ALTER TABLE ip4_subnet DROP FOREIGN KEY FK_SUBNET_NETWORK');
        $this->addSql('ALTER TABLE ip4_subnet DROP FOREIGN KEY FK_SUBNET_USER');
        
        // Eliminar los índices
        $this->addSql('DROP INDEX UNIQ_SUBNET_UUID ON ip4_subnet');
        $this->addSql('DROP INDEX IDX_SUBNET_NETWORK ON ip4_subnet');
        $this->addSql('DROP INDEX IDX_SUBNET_USER ON ip4_subnet');

        $this->addSql('ALTER TABLE ip4_subnet DROP COLUMN id_network');

        // float_group
        $this->addSql('ALTER TABLE ip4_subnet ADD id_floatgroup BIGINT NOT NULL AFTER mask');
        $this->addSql('ALTER TABLE ip4_subnet
                            ADD CONSTRAINT FK_SUBNET_FLOATGROUP FOREIGN KEY (id_floatgroup) REFERENCES ip4_float_group (id)');
        $this->addSql('CREATE INDEX IDX_SUBNET_FLOATGROUP ON ip4_subnet (id_floatgroup)');


        // vm
        $this->addSql('ALTER TABLE ip4_subnet ADD id_vm BIGINT DEFAULT NULL AFTER id_floatgroup');
        $this->addSql('ALTER TABLE ip4_subnet
                            ADD CONSTRAINT FK_SUBNET_VM FOREIGN KEY (id_vm) REFERENCES vm (id)');
        $this->addSql('CREATE INDEX IDX_SUBNET_VM ON ip4_subnet (id_vm)');


        // user
        $this->addSql('ALTER TABLE ip4_subnet MODIFY id_user BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE ip4_subnet
                            ADD CONSTRAINT FK_SUBNET_USER FOREIGN KEY (id_user) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_SUBNET_USER ON ip4_subnet (id_user)');
        

    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE ip4_subnet ADD id_network BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE ip4_subnet ADD CONSTRAINT FK_SUBNET_NETWORK FOREIGN KEY (id_network) REFERENCES ip4_network (id)');
        $this->addSql('CREATE INDEX IDX_SUBNET_NETWORK ON ip4_subnet (id_network)');
    
    
        // Eliminar relación con float_group
        $this->addSql('ALTER TABLE ip4_subnet DROP FOREIGN KEY FK_SUBNET_FLOATGROUP');
        $this->addSql('DROP INDEX IDX_SUBNET_FLOATGROUP ON ip4_subnet');
        $this->addSql('ALTER TABLE ip4_subnet DROP COLUMN id_floatgroup');
    
        // Eliminar relación con vm
        $this->addSql('ALTER TABLE ip4_subnet DROP FOREIGN KEY FK_SUBNET_VM');
        $this->addSql('DROP INDEX IDX_SUBNET_VM ON ip4_subnet');
        $this->addSql('ALTER TABLE ip4_subnet DROP COLUMN id_vm');
    
        // Restaurar id_user y eliminar constraint añadida en up
        $this->addSql('ALTER TABLE ip4_subnet DROP FOREIGN KEY FK_SUBNET_USER');
        $this->addSql('DROP INDEX IDX_SUBNET_USER ON ip4_subnet');
        $this->addSql('ALTER TABLE ip4_subnet MODIFY id_user BIGINT DEFAULT NULL');


    }
}
