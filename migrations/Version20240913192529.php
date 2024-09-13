<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240913192529 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE check_ins (id INT AUTO_INCREMENT NOT NULL, visitor_id INT DEFAULT NULL, qr_code_id INT DEFAULT NULL, check_in_time DATETIME DEFAULT NULL, check_out_time DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_DFFFC3DF70BEE6D (visitor_id), INDEX IDX_DFFFC3DF12E4AD80 (qr_code_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE check_ins ADD CONSTRAINT FK_DFFFC3DF70BEE6D FOREIGN KEY (visitor_id) REFERENCES Visitors (id)');
        $this->addSql('ALTER TABLE check_ins ADD CONSTRAINT FK_DFFFC3DF12E4AD80 FOREIGN KEY (qr_code_id) REFERENCES QRCodes (id)');
        $this->addSql('ALTER TABLE CheckIns DROP FOREIGN KEY FK_5DDC2B7312E4AD80');
        $this->addSql('ALTER TABLE CheckIns DROP FOREIGN KEY FK_5DDC2B7370BEE6D');
        $this->addSql('DROP TABLE CheckIns');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE CheckIns (id INT AUTO_INCREMENT NOT NULL, visitor_id INT DEFAULT NULL, qr_code_id INT DEFAULT NULL, check_in_time DATETIME DEFAULT NULL, check_out_time DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_5DDC2B7370BEE6D (visitor_id), INDEX IDX_5DDC2B7312E4AD80 (qr_code_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE CheckIns ADD CONSTRAINT FK_5DDC2B7312E4AD80 FOREIGN KEY (qr_code_id) REFERENCES QRCodes (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE CheckIns ADD CONSTRAINT FK_5DDC2B7370BEE6D FOREIGN KEY (visitor_id) REFERENCES Visitors (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE check_ins DROP FOREIGN KEY FK_DFFFC3DF70BEE6D');
        $this->addSql('ALTER TABLE check_ins DROP FOREIGN KEY FK_DFFFC3DF12E4AD80');
        $this->addSql('DROP TABLE check_ins');
    }
}
