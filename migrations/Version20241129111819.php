<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241129111819 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE Evenements DROP address_name');
        $this->addSql('ALTER TABLE QRUser ADD dateExp VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE User DROP email');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE Evenements ADD address_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE QRUser DROP dateExp');
        $this->addSql('ALTER TABLE User ADD email VARCHAR(255) NOT NULL');
    }
}
