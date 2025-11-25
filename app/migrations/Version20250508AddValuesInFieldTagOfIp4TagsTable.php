<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use GridCP\Common\Domain\Const\Ip4\Tags;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250508AddValuesInFieldTagOfIp4TagsTable extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {

        //$this->addSql('ALTER TABLE ip4_tag DROP INDEX UNIQ_TAG_UUID');
        //$this->addSql('ALTER TABLE ip4_tag DROP INDEX UNIQ_TAG_TAG');

       // $this->addSql('CREATE INDEX IDX_TAG_IP ON ip4_tag (id_ip)');
        $enumValues = implode("', '", [
            Tags::RESERVED,
            Tags::SUSPENDED,
            Tags::BLACKLIST,
            Tags::WHITELIST,
            Tags::QUARANTINE,
            Tags::INPROGRESS
        ]);
    
        $this->addSql("
            ALTER TABLE ip4_tag 
            MODIFY tag ENUM('$enumValues') 
            DEFAULT NULL
        ");
    }
}
