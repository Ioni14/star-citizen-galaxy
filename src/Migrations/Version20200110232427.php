<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200110232427 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change length of name fields for ship, manufacturer and ship_chassis.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE ship CHANGE name name VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE manufacturer CHANGE name name VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE ship_chassis CHANGE name name VARCHAR(50) NOT NULL');

        $this->addSql('
            SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;

            ALTER TABLE ship CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
            ALTER TABLE ship_ship_role CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
            ALTER TABLE ext_log_entries CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
            ALTER TABLE holded_ship CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
            ALTER TABLE manufacturer CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
            ALTER TABLE migration_versions CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
            ALTER TABLE ship_career CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
            ALTER TABLE ship_chassis CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
            ALTER TABLE ship_role CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
            ALTER TABLE user CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

            SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE manufacturer CHANGE name name VARCHAR(30) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`');
        $this->addSql('ALTER TABLE ship CHANGE name name VARCHAR(30) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`');
        $this->addSql('ALTER TABLE ship_chassis CHANGE name name VARCHAR(30) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`');
    }
}
