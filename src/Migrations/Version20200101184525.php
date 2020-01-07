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
            ready_status VARCHAR(30) DEFAULT NULL,
            size VARCHAR(30) DEFAULT NULL,
            pledge_url VARCHAR(255) DEFAULT NULL,
            thumbnail_path VARCHAR(255) DEFAULT NULL,
            picture_path VARCHAR(255) DEFAULT NULL,
            price INT DEFAULT NULL,
            created_at DATETIME NOT NULL COMMENT '(DC2Type:datetimetz_immutable)',
            updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetimetz_immutable)',
            created_by_id CHAR(36) DEFAULT NULL COMMENT '(DC2Type:uuid)',
            updated_by_id CHAR(36) DEFAULT NULL COMMENT '(DC2Type:uuid)',
            UNIQUE INDEX UNIQ_FA30EB24989D9B62 (slug),
            INDEX IDX_FA30EB2463EE729 (chassis_id),
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
        $this->addSql("CREATE TABLE ship_focus (
            id CHAR(36) NOT NULL COMMENT '(DC2Type:uuid)',
            label VARCHAR(30) NOT NULL,
            INDEX label_idx (label),
            PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB");
        $this->addSql("CREATE TABLE ship_ship_focus (
            ship_id CHAR(36) NOT NULL COMMENT '(DC2Type:uuid)',
            ship_focus_id CHAR(36) NOT NULL COMMENT '(DC2Type:uuid)',
            INDEX IDX_B92F2BC7C256317D (ship_id),
            INDEX IDX_B92F2BC7628D7B97 (ship_focus_id),
            PRIMARY KEY(ship_id, ship_focus_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB");
        $this->addSql('ALTER TABLE ship ADD CONSTRAINT FK_FA30EB2463EE729 FOREIGN KEY (chassis_id) REFERENCES ship_chassis (id)');
        $this->addSql('ALTER TABLE ship ADD CONSTRAINT FK_FA30EB24B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ship ADD CONSTRAINT FK_FA30EB24896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE manufacturer ADD CONSTRAINT FK_3D0AE6DCB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE manufacturer ADD CONSTRAINT FK_3D0AE6DC896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ship_chassis ADD CONSTRAINT FK_3BE443B2A23B42D FOREIGN KEY (manufacturer_id) REFERENCES manufacturer (id)');
        $this->addSql('ALTER TABLE ship_chassis ADD CONSTRAINT FK_3BE443B2B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ship_chassis ADD CONSTRAINT FK_3BE443B2896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE holded_ship ADD CONSTRAINT FK_645607B1DEEE62D0 FOREIGN KEY (holder_id) REFERENCES ship (id)');
        $this->addSql('ALTER TABLE holded_ship ADD CONSTRAINT FK_645607B1AB9C6A93 FOREIGN KEY (holded_id) REFERENCES ship (id)');
        $this->addSql('ALTER TABLE ship_ship_focus ADD CONSTRAINT FK_B92F2BC7C256317D FOREIGN KEY (ship_id) REFERENCES ship (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ship_ship_focus ADD CONSTRAINT FK_B92F2BC7628D7B97 FOREIGN KEY (ship_focus_id) REFERENCES ship_focus (id) ON DELETE CASCADE');

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

        $this->addSql("INSERT INTO ship_focus(id, label) VALUES('7236e77f-ad90-47ec-9fc3-ca05c45c263f', 'Starter')");
        $this->addSql("INSERT INTO ship_focus(id, label) VALUES('4feb3099-4b86-424f-bbd0-9d6fb73083af', 'Combat')");
        $this->addSql("INSERT INTO ship_focus(id, label) VALUES('9f8cf8da-6f0f-46e5-a267-ca3a8628f2c0', 'E-Warfare')");
        $this->addSql("INSERT INTO ship_focus(id, label) VALUES('6b755d89-23b6-4162-bc30-3006518c0fac', 'Freight')");
        $this->addSql("INSERT INTO ship_focus(id, label) VALUES('f7d33250-e2b0-43c2-88ea-2bdc34d8e7a2', 'Touring')");
        $this->addSql("INSERT INTO ship_focus(id, label) VALUES('893d0be5-fd57-4699-9c04-2ed7a5ded66e', 'Racing')");
        $this->addSql("INSERT INTO ship_focus(id, label) VALUES('05eaba1c-8273-4a76-8333-bd2cd776ae11', 'Mining')");
        $this->addSql("INSERT INTO ship_focus(id, label) VALUES('b52fccd7-5eab-4cc3-a873-107b23008594', 'Pathfinder')");
        $this->addSql("INSERT INTO ship_focus(id, label) VALUES('bfe94053-948e-4a2e-a43c-36230e8ca196', 'Expedition')");
        $this->addSql("INSERT INTO ship_focus(id, label) VALUES('b6bfd8e7-ec7c-4e60-bbb6-17f0701847a2', 'Medical')");
        $this->addSql("INSERT INTO ship_focus(id, label) VALUES('7f1d9794-f2d5-4954-a1f9-202664e6f374', 'Bomber')");
        $this->addSql("INSERT INTO ship_focus(id, label) VALUES('becfe7b2-3932-4268-baad-e8f60b1cc417', 'Refuelling')");
        $this->addSql("INSERT INTO ship_focus(id, label) VALUES('538839ce-cd1e-42c7-8b02-35aeb070092c', 'Transport')");
        $this->addSql("INSERT INTO ship_focus(id, label) VALUES('0c979ad9-9372-486f-a5ef-fffd6841fc48', 'Frigate')");
        $this->addSql("INSERT INTO ship_focus(id, label) VALUES('607b8a84-bf13-44a2-9d1f-ecfee5fd7d58', 'Cargo')");
        $this->addSql("INSERT INTO ship_focus(id, label) VALUES('d65d7992-5f4c-4b68-b461-26cee4ed524d', 'Destroyer')");
        $this->addSql("INSERT INTO ship_focus(id, label) VALUES('bb0a910b-7c1d-4a0b-bc5a-29e76b57fedc', 'Science')");
        $this->addSql("INSERT INTO ship_focus(id, label) VALUES('133be469-d901-4003-96be-ca028c56b8b8', 'Repair')");
        $this->addSql("INSERT INTO ship_focus(id, label) VALUES('8c5b14be-c41e-4d70-8c34-c1663643c1ce', 'Passenger')");
        $this->addSql("INSERT INTO ship_focus(id, label) VALUES('6129d90b-73be-418f-8dfb-7cee72e68f7e', 'Luxury')");
        $this->addSql("INSERT INTO ship_focus(id, label) VALUES('4ff0589f-0c90-4974-9ee1-ec990ca2e688', 'Construction')");
        $this->addSql("INSERT INTO ship_focus(id, label) VALUES('2574bc17-159a-4c75-b3d9-120cd226cf34', 'Carrier')");
        $this->addSql("INSERT INTO ship_focus(id, label) VALUES('a7e7fcfd-6744-452b-9cb3-d48ea9b51644', 'Salvage')");
        $this->addSql("INSERT INTO ship_focus(id, label) VALUES('912fa9f7-808e-451a-a35e-d8174abda502', 'Military')");
        $this->addSql("INSERT INTO ship_focus(id, label) VALUES('e59ee7c1-bcf1-4817-83cb-99e2e7aad1d6', 'Minelayer')");
        $this->addSql("INSERT INTO ship_focus(id, label) VALUES('378e6163-c2bf-40e9-a521-4cff3123a487', 'Interceptor')");
        $this->addSql("INSERT INTO ship_focus(id, label) VALUES('8f3ec376-1c8b-4981-95e0-88c8507fd2cf', 'News')");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE ext_log_entries');

        $this->addSql('ALTER TABLE ship_ship_focus DROP FOREIGN KEY FK_B92F2BC7628D7B97');
        $this->addSql('ALTER TABLE manufacturer DROP FOREIGN KEY FK_3D0AE6DCB03A8386');
        $this->addSql('ALTER TABLE manufacturer DROP FOREIGN KEY FK_3D0AE6DC896DBBDE');
        $this->addSql('ALTER TABLE ship DROP FOREIGN KEY FK_FA30EB2463EE729');
        $this->addSql('ALTER TABLE ship DROP FOREIGN KEY FK_FA30EB24B03A8386');
        $this->addSql('ALTER TABLE ship DROP FOREIGN KEY FK_FA30EB24896DBBDE');
        $this->addSql('ALTER TABLE ship_chassis DROP FOREIGN KEY FK_3BE443B2A23B42D');
        $this->addSql('ALTER TABLE ship_chassis DROP FOREIGN KEY FK_3BE443B2B03A8386');
        $this->addSql('ALTER TABLE ship_chassis DROP FOREIGN KEY FK_3BE443B2896DBBDE');
        $this->addSql('DROP TABLE ship_ship_focus');
        $this->addSql('DROP TABLE ship_focus');
        $this->addSql('DROP TABLE holded_ship');
        $this->addSql('DROP TABLE ship');
        $this->addSql('DROP TABLE manufacturer');
        $this->addSql('DROP TABLE ship_chassis');
        $this->addSql('DROP TABLE user');
    }
}
