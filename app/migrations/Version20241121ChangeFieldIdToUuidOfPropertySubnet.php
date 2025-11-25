<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;





/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241121ChangeFieldIdToUuidOfPropertySubnet extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'To keep the relation when there is logical deletion.';
    }

    public function up(Schema $schema): void
    {
        // Crear el índice en ip4_subnet.uuid
    $this->addSql('CREATE INDEX IDX_IP4_SUBNET_UUID ON ip4_subnet (uuid)');

    // Agregar la columna subnet_uuid
    $this->addSql('ALTER TABLE property_ip4_subnet ADD subnet_uuid VARCHAR(36) DEFAULT NULL');

    // Poblar subnet_uuid con los valores de uuid correspondientes
    $this->addSql('
        UPDATE property_ip4_subnet ps
        JOIN ip4_subnet s ON ps.subnet_id = s.id
        SET ps.subnet_uuid = s.uuid
    ');

    // Asegurar que subnet_uuid no tenga valores NULL
    $this->addSql('ALTER TABLE property_ip4_subnet MODIFY subnet_uuid VARCHAR(36) NOT NULL');

    // Eliminar la clave foránea de subnet_id y la columna
    $this->addSql('ALTER TABLE property_ip4_subnet DROP FOREIGN KEY FK_SUBNET_PROPERTY_IP4SUBNET');
    $this->addSql('ALTER TABLE property_ip4_subnet DROP COLUMN subnet_id');

    // Agregar la clave foránea para subnet_uuid
    $this->addSql('
        ALTER TABLE property_ip4_subnet
        ADD CONSTRAINT FK_SUBNET_PROPERTY_IP4SUBNET_UUID
        FOREIGN KEY (subnet_uuid) REFERENCES ip4_subnet(uuid)
    ');
    }

    public function down(Schema $schema): void
    {
        // Eliminar la clave foránea de subnet_uuid
        $this->addSql('ALTER TABLE property_ip4_subnet DROP FOREIGN KEY FK_SUBNET_PROPERTY_IP4SUBNET_UUID');

        // Restaurar la columna subnet_id
        $this->addSql('ALTER TABLE property_ip4_subnet ADD subnet_id BIGINT NOT NULL');

        // Restaurar la clave foránea para subnet_id
        $this->addSql('
            ALTER TABLE property_ip4_subnet
            ADD CONSTRAINT FK_SUBNET_PROPERTY_IP4SUBNET
            FOREIGN KEY (subnet_id) REFERENCES ip4_subnet(id)
        ');

        // Eliminar la columna subnet_uuid
        $this->addSql('ALTER TABLE property_ip4_subnet DROP COLUMN subnet_uuid');

        // Eliminar el índice de ip4_subnet.uuid
        $this->addSql('DROP INDEX IF EXISTS IDX_IP4_SUBNET_UUID ON ip4_subnet');
    }
}
