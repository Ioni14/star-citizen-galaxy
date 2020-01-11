<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200111103818 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add beam on ship.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE ship ADD beam DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE ship DROP beam');
    }
}
