<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240910124756 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evenements DROP FOREIGN KEY FK_E10AD400EAE6F2D2');
        $this->addSql('DROP INDEX IDX_E10AD400EAE6F2D2 ON evenements');
        $this->addSql('ALTER TABLE evenements CHANGE departement departement_id INT NOT NULL');
        $this->addSql('ALTER TABLE evenements ADD CONSTRAINT FK_E10AD400CCF9E01E FOREIGN KEY (departement_id) REFERENCES departements (id)');
        $this->addSql('CREATE INDEX IDX_E10AD400CCF9E01E ON evenements (departement_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evenements DROP FOREIGN KEY FK_E10AD400CCF9E01E');
        $this->addSql('DROP INDEX IDX_E10AD400CCF9E01E ON evenements');
        $this->addSql('ALTER TABLE evenements CHANGE departement_id departement INT NOT NULL');
        $this->addSql('ALTER TABLE evenements ADD CONSTRAINT FK_E10AD400EAE6F2D2 FOREIGN KEY (departement) REFERENCES departements (id)');
        $this->addSql('CREATE INDEX IDX_E10AD400EAE6F2D2 ON evenements (departement)');
    }
}
