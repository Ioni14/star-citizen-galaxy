<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200101184525 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE ship (
            id CHAR(36) NOT NULL COMMENT '(DC2Type:uuid)',
            chassis_id CHAR(36) NOT NULL COMMENT '(DC2Type:uuid)',
            name VARCHAR(30) NOT NULL,
            slug VARCHAR(50) NOT NULL,
            height DOUBLE PRECISION DEFAULT NULL,
            length DOUBLE PRECISION DEFAULT NULL,
            max_crew INT DEFAULT NULL,
            min_crew INT DEFAULT NULL,
            career_id CHAR(36) DEFAULT NULL COMMENT '(DC2Type:uuid)',
            ready_status VARCHAR(30) DEFAULT NULL,
            size VARCHAR(30) DEFAULT NULL,
            cargo_capacity INT DEFAULT NULL,
            pledge_url VARCHAR(255) DEFAULT NULL,
            thumbnail_path VARCHAR(255) DEFAULT NULL,
            picture_path VARCHAR(255) DEFAULT NULL,
            standalone_price INT DEFAULT NULL,
            warbond_price INT DEFAULT NULL,
            created_at DATETIME NOT NULL COMMENT '(DC2Type:datetimetz_immutable)',
            updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetimetz_immutable)',
            created_by_id CHAR(36) DEFAULT NULL COMMENT '(DC2Type:uuid)',
            updated_by_id CHAR(36) DEFAULT NULL COMMENT '(DC2Type:uuid)',
            UNIQUE INDEX UNIQ_FA30EB24989D9B62 (slug),
            INDEX IDX_FA30EB2463EE729 (chassis_id),
            INDEX IDX_FA30EB24B58CDA09 (career_id),
            INDEX IDX_FA30EB24B03A8386 (created_by_id),
            INDEX IDX_FA30EB24896DBBDE (updated_by_id),
            INDEX name_idx (name),
            PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB");
        $this->addSql("CREATE TABLE manufacturer (
            id CHAR(36) NOT NULL COMMENT '(DC2Type:uuid)',
            name VARCHAR(30) NOT NULL,
            slug VARCHAR(50) NOT NULL,
            code VARCHAR(10) NOT NULL,
            created_at DATETIME NOT NULL COMMENT '(DC2Type:datetimetz_immutable)',
            updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetimetz_immutable)',
            created_by_id CHAR(36) DEFAULT NULL COMMENT '(DC2Type:uuid)',
            updated_by_id CHAR(36) DEFAULT NULL COMMENT '(DC2Type:uuid)',
            UNIQUE INDEX UNIQ_3D0AE6DC989D9B62 (slug),
            INDEX IDX_3D0AE6DCB03A8386 (created_by_id),
            INDEX IDX_3D0AE6DC896DBBDE (updated_by_id),
            INDEX name_idx (name),
            INDEX code_idx (code),
            PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB");
        $this->addSql("CREATE TABLE ship_chassis (
            id CHAR(36) NOT NULL COMMENT '(DC2Type:uuid)',
            manufacturer_id CHAR(36) NOT NULL COMMENT '(DC2Type:uuid)',
            name VARCHAR(30) NOT NULL, slug VARCHAR(50) NOT NULL,
            created_at DATETIME NOT NULL COMMENT '(DC2Type:datetimetz_immutable)',
            updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetimetz_immutable)',
            created_by_id CHAR(36) DEFAULT NULL COMMENT '(DC2Type:uuid)',
            updated_by_id CHAR(36) DEFAULT NULL COMMENT '(DC2Type:uuid)',
            UNIQUE INDEX UNIQ_3BE443B2989D9B62 (slug),
            INDEX IDX_3BE443B2A23B42D (manufacturer_id),
            INDEX IDX_3BE443B2B03A8386 (created_by_id),
            INDEX IDX_3BE443B2896DBBDE (updated_by_id),
            INDEX name_idx (name),
            PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB");
        $this->addSql("CREATE TABLE user (
            id CHAR(36) NOT NULL COMMENT '(DC2Type:uuid)',
            username VARCHAR(50) NOT NULL,
            roles JSON NOT NULL COMMENT '(DC2Type:json_array)',
            nickname VARCHAR(50) DEFAULT NULL,
            discord_id VARCHAR(50) DEFAULT NULL,
            discord_tag CHAR(4) DEFAULT NULL,
            created_at DATETIME NOT NULL COMMENT '(DC2Type:datetimetz_immutable)',
            updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetimetz_immutable)',
            INDEX discord_idx (discord_id),
            INDEX username_idx (username),
            PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB");
        $this->addSql("CREATE TABLE holded_ship (
            holder_id CHAR(36) NOT NULL COMMENT '(DC2Type:uuid)',
            holded_id CHAR(36) NOT NULL COMMENT '(DC2Type:uuid)',
            quantity INT DEFAULT 1 NOT NULL,
            INDEX IDX_645607B1DEEE62D0 (holder_id),
            INDEX IDX_645607B1AB9C6A93 (holded_id),
            PRIMARY KEY(holder_id, holded_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB");
        $this->addSql("CREATE TABLE ship_career (
            id CHAR(36) NOT NULL COMMENT '(DC2Type:uuid)',
            label VARCHAR(30) NOT NULL,
            INDEX label_idx (label),
            PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB");
        $this->addSql("CREATE TABLE ship_role (
            id CHAR(36) NOT NULL COMMENT '(DC2Type:uuid)',
            label VARCHAR(30) NOT NULL,
            INDEX label_idx (label),
            PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB");
        $this->addSql("CREATE TABLE ship_ship_role (
            ship_id CHAR(36) NOT NULL COMMENT '(DC2Type:uuid)',
            ship_role_id CHAR(36) NOT NULL COMMENT '(DC2Type:uuid)',
            INDEX IDX_3C17240C256317D (ship_id),
            INDEX IDX_3C17240D82E12C1 (ship_role_id),
            PRIMARY KEY(ship_id, ship_role_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB");
        $this->addSql('ALTER TABLE ship ADD CONSTRAINT FK_FA30EB2463EE729 FOREIGN KEY (chassis_id) REFERENCES ship_chassis (id)');
        $this->addSql('ALTER TABLE ship ADD CONSTRAINT FK_FA30EB24B58CDA09 FOREIGN KEY (career_id) REFERENCES ship_career (id)');
        $this->addSql('ALTER TABLE ship ADD CONSTRAINT FK_FA30EB24B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ship ADD CONSTRAINT FK_FA30EB24896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE manufacturer ADD CONSTRAINT FK_3D0AE6DCB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE manufacturer ADD CONSTRAINT FK_3D0AE6DC896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ship_chassis ADD CONSTRAINT FK_3BE443B2A23B42D FOREIGN KEY (manufacturer_id) REFERENCES manufacturer (id)');
        $this->addSql('ALTER TABLE ship_chassis ADD CONSTRAINT FK_3BE443B2B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ship_chassis ADD CONSTRAINT FK_3BE443B2896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE holded_ship ADD CONSTRAINT FK_645607B1DEEE62D0 FOREIGN KEY (holder_id) REFERENCES ship (id)');
        $this->addSql('ALTER TABLE holded_ship ADD CONSTRAINT FK_645607B1AB9C6A93 FOREIGN KEY (holded_id) REFERENCES ship (id)');
        $this->addSql('ALTER TABLE ship_ship_role ADD CONSTRAINT FK_3C17240C256317D FOREIGN KEY (ship_id) REFERENCES ship (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ship_ship_role ADD CONSTRAINT FK_3C17240D82E12C1 FOREIGN KEY (ship_role_id) REFERENCES ship_role (id) ON DELETE CASCADE');

        $this->addSql("CREATE TABLE ext_log_entries (
            id INT AUTO_INCREMENT NOT NULL,
            action VARCHAR(8) NOT NULL,
            logged_at DATETIME NOT NULL,
            object_id VARCHAR(64) DEFAULT NULL,
            object_class VARCHAR(255) NOT NULL,
            version INT NOT NULL,
            data LONGTEXT DEFAULT NULL COMMENT '(DC2Type:array)',
            username VARCHAR(255) DEFAULT NULL,
            INDEX log_class_lookup_idx (object_class),
            INDEX log_date_lookup_idx (logged_at),
            INDEX log_user_lookup_idx (username),
            INDEX log_version_lookup_idx (object_id, object_class, version),
            PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB ROW_FORMAT = DYNAMIC");

        $this->addSql("INSERT INTO ship_career(id, label) VALUES('2caf6940-9405-4eff-8a97-1f75412e51ce', 'Combat')");
        $this->addSql("INSERT INTO ship_career(id, label) VALUES('e28fefdd-846c-4911-b1ba-250eed27bfad', 'Transport')");
        $this->addSql("INSERT INTO ship_career(id, label) VALUES('9ed86581-5d55-40bc-bfbc-df08e37750a6', 'Exploration')");
        $this->addSql("INSERT INTO ship_career(id, label) VALUES('6cfdb78f-1839-4e72-9eb0-ba475b4db0c3', 'Industrial')");
        $this->addSql("INSERT INTO ship_career(id, label) VALUES('696a0f4d-6fe1-4b1f-8b37-2c2355824657', 'Support')");
        $this->addSql("INSERT INTO ship_career(id, label) VALUES('b0e7049a-5692-45e4-b9d0-ae0a6a9bedd2', 'Competition')");

        $this->addSql("INSERT INTO ship_role(id, label) VALUES('8e30b822-25a6-4716-8429-216b300115bd', 'Fighters')");
        $this->addSql("INSERT INTO ship_role(id, label) VALUES('a01cda17-37e5-434d-b2be-998f2632e9aa', 'Interdiction')");
        $this->addSql("INSERT INTO ship_role(id, label) VALUES('a4399327-d77b-449c-ab0f-f4e923638f20', 'Drop Ship')");
        $this->addSql("INSERT INTO ship_role(id, label) VALUES('08d43f2e-6cd4-4b9e-82fe-90b17f3eb4e4', 'Bomber')");
        $this->addSql("INSERT INTO ship_role(id, label) VALUES('d89c0ac7-7b1b-46ab-b733-2001e80ccefb', 'Freight')");
        $this->addSql("INSERT INTO ship_role(id, label) VALUES('0a5368ec-7853-4083-9dfa-38896ec94d5f', 'Passenger')");
        $this->addSql("INSERT INTO ship_role(id, label) VALUES('f320b0a9-2cc0-486b-b42c-40712434b26b', 'Data')");
        $this->addSql("INSERT INTO ship_role(id, label) VALUES('922f426f-dd12-4d2a-9fdb-19c5a9cb2417', 'Pathfinder')");
        $this->addSql("INSERT INTO ship_role(id, label) VALUES('cef8fc55-33b5-40e3-b093-f63c4ad8428d', 'Expedition')");
        $this->addSql("INSERT INTO ship_role(id, label) VALUES('884e1f12-fd98-415f-a36f-00af8454d141', 'Touring')");
        $this->addSql("INSERT INTO ship_role(id, label) VALUES('f935e2a7-a759-4a1f-accf-a6d145acd710', 'Mining')");
        $this->addSql("INSERT INTO ship_role(id, label) VALUES('a7187e41-2759-4fa7-b99a-a55ce4899443', 'Salvage')");
        $this->addSql("INSERT INTO ship_role(id, label) VALUES('29b58278-21a5-42bb-900c-feaf884d890b', 'Science')");
        $this->addSql("INSERT INTO ship_role(id, label) VALUES('4fc5903a-cab5-424d-bdfb-86b2b5c91cca', 'Agriculture')");
        $this->addSql("INSERT INTO ship_role(id, label) VALUES('1573a876-1bab-4316-99cf-ee35e2fb10ce', 'Medical')");
        $this->addSql("INSERT INTO ship_role(id, label) VALUES('d73e175a-5601-4536-9526-935765edcd6a', 'Refueling')");
        $this->addSql("INSERT INTO ship_role(id, label) VALUES('6e59ed0c-250a-4b2b-8f95-f3c1238de24b', 'Repair')");
        $this->addSql("INSERT INTO ship_role(id, label) VALUES('f8f03bd9-4095-43f3-b7ee-6bcd21a87f78', 'Reporting')");
        $this->addSql("INSERT INTO ship_role(id, label) VALUES('cc2bc9f5-0277-4a55-bfb9-ef128bf698fa', 'Racing')");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE ext_log_entries');

        $this->addSql('ALTER TABLE ship_ship_role DROP FOREIGN KEY FK_3C17240D82E12C1');
        $this->addSql('ALTER TABLE manufacturer DROP FOREIGN KEY FK_3D0AE6DCB03A8386');
        $this->addSql('ALTER TABLE manufacturer DROP FOREIGN KEY FK_3D0AE6DC896DBBDE');
        $this->addSql('ALTER TABLE ship DROP FOREIGN KEY FK_FA30EB24B58CDA09');
        $this->addSql('ALTER TABLE ship DROP FOREIGN KEY FK_FA30EB2463EE729');
        $this->addSql('ALTER TABLE ship DROP FOREIGN KEY FK_FA30EB24B03A8386');
        $this->addSql('ALTER TABLE ship DROP FOREIGN KEY FK_FA30EB24896DBBDE');
        $this->addSql('ALTER TABLE ship_chassis DROP FOREIGN KEY FK_3BE443B2A23B42D');
        $this->addSql('ALTER TABLE ship_chassis DROP FOREIGN KEY FK_3BE443B2B03A8386');
        $this->addSql('ALTER TABLE ship_chassis DROP FOREIGN KEY FK_3BE443B2896DBBDE');
        $this->addSql('DROP TABLE ship_ship_role');
        $this->addSql('DROP TABLE ship_role');
        $this->addSql('DROP TABLE ship_career');
        $this->addSql('DROP TABLE holded_ship');
        $this->addSql('DROP TABLE ship');
        $this->addSql('DROP TABLE manufacturer');
        $this->addSql('DROP TABLE ship_chassis');
        $this->addSql('DROP TABLE user');
    }
}
