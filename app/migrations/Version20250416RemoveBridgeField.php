<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250416RemoveBridgeField extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("
        SET @exists := (
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_NAME = 'node' 
              AND COLUMN_NAME = 'bridge' 
              AND TABLE_SCHEMA = DATABASE()
        );
        SET @sql := IF(@exists > 0, 'ALTER TABLE node DROP COLUMN bridge', 'SELECT 1');
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    ");
    }
}
