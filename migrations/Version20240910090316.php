<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240910090316 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE qrcodes (id INT AUTO_INCREMENT NOT NULL, visitor_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL, uidn VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, expiration_date DATE DEFAULT NULL, status TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_B2169E7B70BEE6D (visitor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE qrcodes ADD CONSTRAINT FK_B2169E7B70BEE6D FOREIGN KEY (visitor_id) REFERENCES visitors (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE qrcodes DROP FOREIGN KEY FK_B2169E7B70BEE6D');
        $this->addSql('DROP TABLE qrcodes');
    }
}
