<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241026ChangeLengthSomeNodeFields extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE node MODIFY COLUMN storage VARCHAR(255) DEFAULT NULL");
        $this->addSql("ALTER TABLE node MODIFY COLUMN storage_iso VARCHAR(255) DEFAULT NULL");
        $this->addSql("ALTER TABLE node MODIFY COLUMN storage_image VARCHAR(255) DEFAULT NULL");
        $this->addSql("ALTER TABLE node MODIFY COLUMN storage_backup VARCHAR(255) DEFAULT NULL");
        $this->addSql("ALTER TABLE node MODIFY COLUMN network_interface VARCHAR(255) DEFAULT NULL");
    }
    
    public function down(Schema $schema): void
    {
        $this->addSql("ALTER TABLE node MODIFY COLUMN storage VARCHAR(200) DEFAULT NULL");
        $this->addSql("ALTER TABLE node MODIFY COLUMN storage_iso VARCHAR(20) DEFAULT NULL");
        $this->addSql("ALTER TABLE node MODIFY COLUMN storage_image VARCHAR(20) DEFAULT NULL");
        $this->addSql("ALTER TABLE node MODIFY COLUMN storage_backup VARCHAR(20) DEFAULT NULL");
        $this->addSql("ALTER TABLE node MODIFY COLUMN network_interface VARCHAR(20) DEFAULT NULL");
    }
}
