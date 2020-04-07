<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200407173648 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove warbond price and change standalone price to pledge cost on Ship table.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE ship DROP warbond_price, CHANGE standalone_price pledge_cost INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE ship ADD warbond_price INT DEFAULT NULL, CHANGE pledge_cost standalone_price INT DEFAULT NULL');
    }
}
