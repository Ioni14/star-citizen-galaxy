<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200101184525 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add ship, ship_chassis, manufacturer and user tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE ship (
            id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid_binary)',
            chassis_id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid_binary)',
            holder_id BINARY(16) DEFAULT NULL COMMENT '(DC2Type:uuid_binary)',
            created_by_id BINARY(16) DEFAULT NULL COMMENT '(DC2Type:uuid_binary)',
            updated_by_id BINARY(16) DEFAULT NULL COMMENT '(DC2Type:uuid_binary)',
            name VARCHAR(30) NOT NULL,
            slug VARCHAR(50) NOT NULL,
            rsi_id INT DEFAULT NULL,
            height NUMERIC(10, 4) DEFAULT NULL,
            length NUMERIC(10, 4) DEFAULT NULL,
            max_crew INT DEFAULT NULL,
            min_crew INT DEFAULT NULL,
            ready_status VARCHAR(30) DEFAULT NULL,
            size VARCHAR(30) DEFAULT NULL,
            focus VARCHAR(30) DEFAULT NULL,
            pledge_url VARCHAR(255) DEFAULT NULL,
            thumbnail_uri VARCHAR(255) DEFAULT NULL,
            picture_uri VARCHAR(255) DEFAULT NULL,
            created_at DATETIME NOT NULL COMMENT '(DC2Type:datetimetz_immutable)',
            updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetimetz_immutable)',
            UNIQUE INDEX UNIQ_FA30EB24989D9B62 (slug),
            INDEX IDX_FA30EB2463EE729 (chassis_id),
            INDEX IDX_FA30EB24DEEE62D0 (holder_id),
            INDEX IDX_FA30EB24B03A8386 (created_by_id),
            INDEX IDX_FA30EB24896DBBDE (updated_by_id),
            INDEX name_idx (name),
            PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB");
        $this->addSql("CREATE TABLE manufacturer (
            id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid_binary)',
            created_by_id BINARY(16) DEFAULT NULL COMMENT '(DC2Type:uuid_binary)',
            updated_by_id BINARY(16) DEFAULT NULL COMMENT '(DC2Type:uuid_binary)',
            name VARCHAR(30) NOT NULL,
            slug VARCHAR(50) NOT NULL,
            code VARCHAR(10) NOT NULL,
            rsi_id INT DEFAULT NULL,
            thumbnail_uri VARCHAR(255) DEFAULT NULL,
            picture_uri VARCHAR(255) DEFAULT NULL,
            created_at DATETIME NOT NULL COMMENT '(DC2Type:datetimetz_immutable)',
            updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetimetz_immutable)',
            UNIQUE INDEX UNIQ_3D0AE6DC989D9B62 (slug),
            INDEX IDX_3D0AE6DCB03A8386 (created_by_id),
            INDEX IDX_3D0AE6DC896DBBDE (updated_by_id),
            INDEX name_idx (name),
            INDEX code_idx (code),
            PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB");
        $this->addSql("CREATE TABLE ship_chassis (
            id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid_binary)',
            manufacturer_id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid_binary)',
            created_by_id BINARY(16) DEFAULT NULL COMMENT '(DC2Type:uuid_binary)',
            updated_by_id BINARY(16) DEFAULT NULL COMMENT '(DC2Type:uuid_binary)',
            name VARCHAR(30) NOT NULL, slug VARCHAR(50) NOT NULL,
            rsi_id INT DEFAULT NULL,
            created_at DATETIME NOT NULL COMMENT '(DC2Type:datetimetz_immutable)',
            updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetimetz_immutable)',
            UNIQUE INDEX UNIQ_3BE443B2989D9B62 (slug),
            INDEX IDX_3BE443B2A23B42D (manufacturer_id),
            INDEX IDX_3BE443B2B03A8386 (created_by_id),
            INDEX IDX_3BE443B2896DBBDE (updated_by_id),
            INDEX name_idx (name),
            PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB");
        $this->addSql("CREATE TABLE user (
            id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid_binary)',
            username VARCHAR(255) NOT NULL,
            roles JSON NOT NULL COMMENT '(DC2Type:json_array)',
            nickname VARCHAR(255) DEFAULT NULL,
            discord_id VARCHAR(255) DEFAULT NULL,
            discord_tag VARCHAR(15) DEFAULT NULL,
            created_at DATETIME NOT NULL COMMENT '(DC2Type:datetimetz_immutable)',
            updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetimetz_immutable)',
            INDEX discord_idx (discord_id),
            INDEX username_idx (username),
            PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB");
        $this->addSql('ALTER TABLE ship ADD CONSTRAINT FK_FA30EB2463EE729 FOREIGN KEY (chassis_id) REFERENCES ship_chassis (id)');
        $this->addSql('ALTER TABLE ship ADD CONSTRAINT FK_FA30EB24DEEE62D0 FOREIGN KEY (holder_id) REFERENCES ship (id)');
        $this->addSql('ALTER TABLE ship ADD CONSTRAINT FK_FA30EB24B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ship ADD CONSTRAINT FK_FA30EB24896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE manufacturer ADD CONSTRAINT FK_3D0AE6DCB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE manufacturer ADD CONSTRAINT FK_3D0AE6DC896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ship_chassis ADD CONSTRAINT FK_3BE443B2A23B42D FOREIGN KEY (manufacturer_id) REFERENCES manufacturer (id)');
        $this->addSql('ALTER TABLE ship_chassis ADD CONSTRAINT FK_3BE443B2B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ship_chassis ADD CONSTRAINT FK_3BE443B2896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE manufacturer DROP FOREIGN KEY FK_3D0AE6DCB03A8386');
        $this->addSql('ALTER TABLE manufacturer DROP FOREIGN KEY FK_3D0AE6DC896DBBDE');
        $this->addSql('ALTER TABLE ship DROP FOREIGN KEY FK_FA30EB2463EE729');
        $this->addSql('ALTER TABLE ship DROP FOREIGN KEY FK_FA30EB24DEEE62D0');
        $this->addSql('ALTER TABLE ship DROP FOREIGN KEY FK_FA30EB24B03A8386');
        $this->addSql('ALTER TABLE ship DROP FOREIGN KEY FK_FA30EB24896DBBDE');
        $this->addSql('ALTER TABLE ship_chassis DROP FOREIGN KEY FK_3BE443B2A23B42D');
        $this->addSql('ALTER TABLE ship_chassis DROP FOREIGN KEY FK_3BE443B2B03A8386');
        $this->addSql('ALTER TABLE ship_chassis DROP FOREIGN KEY FK_3BE443B2896DBBDE');
        $this->addSql('DROP TABLE ship');
        $this->addSql('DROP TABLE manufacturer');
        $this->addSql('DROP TABLE ship_chassis');
        $this->addSql('DROP TABLE user');
    }
}
