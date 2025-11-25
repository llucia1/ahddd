<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241016CreateTables extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void// NOSONAR
    {
        // Create tables

        $this->addSql('CREATE TABLE IF NOT EXISTS auth (
        id BIGINT AUTO_INCREMENT NOT NULL,
        uuid VARCHAR(36) NOT NULL,
        email VARCHAR(180) NOT NULL,
        username VARCHAR(180) NOT NULL,
        roles JSON NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at DATETIME NOT NULL,
        updated_at DATETIME DEFAULT NULL,
        PRIMARY KEY(id),
        UNIQUE INDEX idx_uuid (uuid),
        UNIQUE INDEX idx_email (email),
        UNIQUE INDEX idx_username (username)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci');


        $this->addSql('CREATE TABLE IF NOT EXISTS user (
        id BIGINT AUTO_INCREMENT NOT NULL,
        uuid VARCHAR(36) NOT NULL,
        email VARCHAR(180) NOT NULL,
        first_name VARCHAR(180) NOT NULL,
        last_name VARCHAR(180) NOT NULL,
        auth_uuid  VARCHAR(36) NOT NULL,
        created_at DATETIME NOT NULL,
        updated_at DATETIME DEFAULT NULL,
        PRIMARY KEY(id),
        UNIQUE INDEX idx_uuid (uuid),
        UNIQUE INDEX idx_email (email),
        INDEX idx_auth_uuid (auth_uuid),
        CONSTRAINT FK_USER_AUTH FOREIGN KEY (auth_uuid) REFERENCES auth (uuid)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci');

        $this->addSql('CREATE TABLE IF NOT EXISTS node (
        id BIGINT AUTO_INCREMENT NOT NULL,
        uuid VARCHAR(36) NOT NULL,
        gcp_node_name VARCHAR(250) NOT NULL,
        pve_node_name VARCHAR(250) NOT NULL,
        pve_hostname VARCHAR(250) NOT NULL,
        pve_username VARCHAR(250) NOT NULL,
        pve_password VARCHAR(250) NOT NULL,
        pve_realm VARCHAR(30) NOT NULL,
        pve_port INT not null ,
        pve_ip VARCHAR(20) NOT NULL,
        ssh_port INT DEFAULT NULL,
        timezone VARCHAR(50) DEFAULT NULL,
        keyboard VARCHAR(3) DEFAULT NULL,
        display VARCHAR(20) DEFAULT NULL,
        storage VARCHAR(255) DEFAULT NULL,
        storage_iso VARCHAR(255) DEFAULT NULL,
        storage_image VARCHAR(255) DEFAULT NULL,
        storage_backup VARCHAR(255) DEFAULT NULL,
        network_interface VARCHAR(255) DEFAULT NULL,
        active BOOL NOT NULL,
        created_at DATETIME NOT NULL,
        updated_at DATETIME DEFAULT NULL,
        UNIQUE INDEX UNIQ_NODE_UUID (uuid),
        PRIMARY KEY(id)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci');

        $this->addSql('CREATE TABLE IF NOT EXISTS ip4_float_group (
        id BIGINT AUTO_INCREMENT NOT NULL,
        uuid VARCHAR(36) NOT NULL,
        name VARCHAR(255) NOT NULL,
        active BOOL NOT NULL,
        created_at DATETIME NOT NULL,
        updated_at DATETIME DEFAULT NULL,
        PRIMARY KEY(id),
        UNIQUE INDEX UNIQ_FLOAT_GROUP_UUID (uuid)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci');

        $this->addSql('CREATE TABLE IF NOT EXISTS ip4_network (
         id BIGINT AUTO_INCREMENT NOT NULL,
        uuid VARCHAR(36) NOT NULL,
        name VARCHAR(255) NOT NULL,
        name_server_1 VARCHAR(15) DEFAULT NULL,
        name_server_2 VARCHAR(15) DEFAULT NULL,
        name_server_3 VARCHAR(15) DEFAULT NULL,
        name_server_4 VARCHAR(15) DEFAULT NULL,
        priority INT NOT NULL,
        selectable_by_client BOOL NOT NULL,
        free INT NOT NULL,
        netmask VARCHAR(15) NOT NULL,
        gateway VARCHAR(15) NOT NULL,
        broadcast VARCHAR(15) NOT NULL,
        no_arp BOOL NOT NULL,
        rir BOOL NOT NULL,
        active BOOL NOT NULL,
        created_at DATETIME NOT NULL,
        updated_at DATETIME DEFAULT NULL,
        PRIMARY KEY(id),
        UNIQUE INDEX UNIQ_NETWORK_UUID (uuid)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci');

        $this->addSql('CREATE TABLE IF NOT EXISTS ip4_subnet (
        id BIGINT AUTO_INCREMENT NOT NULL,
        uuid VARCHAR(36) NOT NULL,
        id_network BIGINT DEFAULT NULL,
        id_user BIGINT DEFAULT NULL,
        ip VARCHAR(15) DEFAULT NULL,
        mask INT NOT NULL,
        active BOOL NOT NULL,
        created_at DATETIME NOT NULL,
        updated_at DATETIME DEFAULT NULL,
        PRIMARY KEY(id),
        UNIQUE INDEX UNIQ_SUBNET_UUID (uuid),
        INDEX IDX_SUBNET_NETWORK (id_network),
        INDEX IDX_SUBNET_USER (id_user),
        INDEX IDX_SUBNET_IP (ip),
        CONSTRAINT FK_SUBNET_NETWORK FOREIGN KEY (id_network) REFERENCES ip4_network (id),
        CONSTRAINT FK_SUBNET_USER FOREIGN KEY (id_user) REFERENCES auth (id)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci');

        $this->addSql('CREATE TABLE IF NOT EXISTS ip4 (
        id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL,
        uuid VARCHAR(255) NOT NULL,
        ip VARCHAR(15) DEFAULT NULL,
        id_network BIGINT NOT NULL,
        active BOOL NOT NULL,
        created_at DATETIME NOT NULL,
        updated_at DATETIME DEFAULT NULL,
        UNIQUE INDEX UNIQ_IP_UUID (uuid),
    /*    INDEX IDX_IP4_USER (id_user),*/
        INDEX IDX_IP4_NETWORK (id_network),
    /*    INDEX IDX_IP4_SUBNET (id_subnet),*/
        PRIMARY KEY(id),
        CONSTRAINT FK_IP4_NETWORK FOREIGN KEY (id_network) REFERENCES ip4_network (id)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci');

        $this->addSql('CREATE TABLE IF NOT EXISTS ip4_tag (
        id BIGINT AUTO_INCREMENT NOT NULL,
        uuid VARCHAR(36) NOT NULL,
        id_ip BIGINT UNSIGNED DEFAULT NULL,
        tag VARCHAR(80) NOT NULL,
        created_at DATETIME NOT NULL,
        updated_at DATETIME DEFAULT NULL,
        UNIQUE INDEX UNIQ_TAG_UUID (uuid),
        UNIQUE INDEX UNIQ_TAG_TAG (tag),
        PRIMARY KEY(id),
        INDEX IDX_TAG_IP (id_ip),
        CONSTRAINT FK_TAG_IP FOREIGN KEY (id_ip) REFERENCES ip4 (id)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci');


    $this->addSql('CREATE TABLE IF NOT EXISTS vm (
        id BIGINT AUTO_INCREMENT NOT NULL,
        uuid VARCHAR(36) NOT NULL,
        id_node BIGINT NOT NULL,
        name VARCHAR(255) DEFAULT NULL,
        cpu VARCHAR(255) NOT NULL,
        cores INT DEFAULT NULL,
        os VARCHAR(255) DEFAULT NULL,
        active TINYINT(1) DEFAULT 1,
        created_at DATETIME NOT NULL,
        updated_at DATETIME DEFAULT NULL,
        PRIMARY KEY(id),
        UNIQUE INDEX idx_uuid (uuid),
        INDEX idx_idnode (id_node),
        CONSTRAINT fk_vm_node FOREIGN KEY (id_node) REFERENCES node (id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
    }
}
