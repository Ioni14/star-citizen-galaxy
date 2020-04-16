<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200412151958 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change type of cargoCapacity to float.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE ship CHANGE cargo_capacity cargo_capacity DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE ship CHANGE cargo_capacity cargo_capacity INT DEFAULT NULL');
    }
}
