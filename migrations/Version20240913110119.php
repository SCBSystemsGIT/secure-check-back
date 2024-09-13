<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240913110119 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE CheckIns (id INT AUTO_INCREMENT NOT NULL, visitor_id INT DEFAULT NULL, qr_code_id INT DEFAULT NULL, check_in_time DATETIME DEFAULT NULL, check_out_time DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_5DDC2B7370BEE6D (visitor_id), INDEX IDX_5DDC2B7312E4AD80 (qr_code_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE CheckIns ADD CONSTRAINT FK_5DDC2B7370BEE6D FOREIGN KEY (visitor_id) REFERENCES Visitors (id)');
        $this->addSql('ALTER TABLE CheckIns ADD CONSTRAINT FK_5DDC2B7312E4AD80 FOREIGN KEY (qr_code_id) REFERENCES QRCodes (id)');
        $this->addSql('ALTER TABLE check_ins DROP FOREIGN KEY FK_DFFFC3DF12E4AD80');
        $this->addSql('ALTER TABLE check_ins DROP FOREIGN KEY FK_DFFFC3DF70BEE6D');
        $this->addSql('DROP TABLE check_ins');
        $this->addSql('ALTER TABLE evenements RENAME INDEX idx_e10ad400ccf9e01e TO IDX_AE57D7D0CCF9E01E');
        $this->addSql('ALTER TABLE qrcodes RENAME INDEX idx_b2169e7b70bee6d TO IDX_BBC68DD570BEE6D');
        $this->addSql('ALTER TABLE requests RENAME INDEX idx_7b85d65170bee6d TO IDX_82F3B40770BEE6D');
        $this->addSql('ALTER TABLE requests RENAME INDEX idx_7b85d651a76ed395 TO IDX_82F3B407A76ED395');
        $this->addSql('ALTER TABLE user RENAME INDEX idx_8d93d649ae80f5df TO IDX_2DA17977AE80F5DF');
        $this->addSql('ALTER TABLE visitors ADD organisationName VARCHAR(255) DEFAULT NULL, ADD idNumber VARCHAR(255) NOT NULL, ADD visitor_type INT DEFAULT NULL');
        $this->addSql('ALTER TABLE visitors RENAME INDEX idx_7b74a43fa76ed395 TO IDX_8202C669A76ED395');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE check_ins (id INT AUTO_INCREMENT NOT NULL, visitor_id INT DEFAULT NULL, qr_code_id INT DEFAULT NULL, check_in_time DATETIME DEFAULT NULL, check_out_time DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_DFFFC3DF12E4AD80 (qr_code_id), INDEX IDX_DFFFC3DF70BEE6D (visitor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE check_ins ADD CONSTRAINT FK_DFFFC3DF12E4AD80 FOREIGN KEY (qr_code_id) REFERENCES qrcodes (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE check_ins ADD CONSTRAINT FK_DFFFC3DF70BEE6D FOREIGN KEY (visitor_id) REFERENCES visitors (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE CheckIns DROP FOREIGN KEY FK_5DDC2B7370BEE6D');
        $this->addSql('ALTER TABLE CheckIns DROP FOREIGN KEY FK_5DDC2B7312E4AD80');
        $this->addSql('DROP TABLE CheckIns');
        $this->addSql('ALTER TABLE Visitors DROP organisationName, DROP idNumber, DROP visitor_type');
        $this->addSql('ALTER TABLE Visitors RENAME INDEX idx_8202c669a76ed395 TO IDX_7B74A43FA76ED395');
        $this->addSql('ALTER TABLE User RENAME INDEX idx_2da17977ae80f5df TO IDX_8D93D649AE80F5DF');
        $this->addSql('ALTER TABLE Requests RENAME INDEX idx_82f3b40770bee6d TO IDX_7B85D65170BEE6D');
        $this->addSql('ALTER TABLE Requests RENAME INDEX idx_82f3b407a76ed395 TO IDX_7B85D651A76ED395');
        $this->addSql('ALTER TABLE Evenements RENAME INDEX idx_ae57d7d0ccf9e01e TO IDX_E10AD400CCF9E01E');
        $this->addSql('ALTER TABLE QRCodes RENAME INDEX idx_bbc68dd570bee6d TO IDX_B2169E7B70BEE6D');
    }
}
