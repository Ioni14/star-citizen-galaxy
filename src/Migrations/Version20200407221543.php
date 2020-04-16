<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200407221543 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add loaner ships.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE loaner_ship (loaner_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', loaned_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', quantity INT DEFAULT 1 NOT NULL, INDEX IDX_618F6E2A434E717A (loaner_id), INDEX IDX_618F6E2A363C7939 (loaned_id), PRIMARY KEY(loaner_id, loaned_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE loaner_ship ADD CONSTRAINT FK_618F6E2A434E717A FOREIGN KEY (loaner_id) REFERENCES ship (id)');
        $this->addSql('ALTER TABLE loaner_ship ADD CONSTRAINT FK_618F6E2A363C7939 FOREIGN KEY (loaned_id) REFERENCES ship (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE loaner_ship');
    }
}
