<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240912134839 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE check_ins (id INT AUTO_INCREMENT NOT NULL, visitor_id INT DEFAULT NULL, qr_code_id INT DEFAULT NULL, check_in_time DATETIME DEFAULT NULL, check_out_time DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_DFFFC3DF70BEE6D (visitor_id), INDEX IDX_DFFFC3DF12E4AD80 (qr_code_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE departements (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, location VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', status TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evenements (id INT AUTO_INCREMENT NOT NULL, departement_id INT NOT NULL, name VARCHAR(255) NOT NULL, location VARCHAR(255) NOT NULL, date_event DATE NOT NULL, time_event TIME NOT NULL, status TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_E10AD400CCF9E01E (departement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE qrcodes (id INT AUTO_INCREMENT NOT NULL, visitor_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL, uidn VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, expiration_date DATE DEFAULT NULL, status TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_B2169E7B70BEE6D (visitor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE requests (id INT AUTO_INCREMENT NOT NULL, visitor_id INT DEFAULT NULL, user_id INT DEFAULT NULL, host VARCHAR(255) NOT NULL, status TINYINT(1) DEFAULT NULL, confirmed TINYINT(1) DEFAULT NULL, request_date DATETIME NOT NULL, response_date DATETIME DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7B85D65170BEE6D (visitor_id), INDEX IDX_7B85D651A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, department_id INT NOT NULL, name VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, role LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', title VARCHAR(255) NOT NULL, create_at DATETIME NOT NULL, updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', status TINYINT(1) NOT NULL, contact VARCHAR(255) NOT NULL, INDEX IDX_8D93D649AE80F5DF (department_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE visitors (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, contact VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7B74A43FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE check_ins ADD CONSTRAINT FK_DFFFC3DF70BEE6D FOREIGN KEY (visitor_id) REFERENCES visitors (id)');
        $this->addSql('ALTER TABLE check_ins ADD CONSTRAINT FK_DFFFC3DF12E4AD80 FOREIGN KEY (qr_code_id) REFERENCES qrcodes (id)');
        $this->addSql('ALTER TABLE evenements ADD CONSTRAINT FK_E10AD400CCF9E01E FOREIGN KEY (departement_id) REFERENCES departements (id)');
        $this->addSql('ALTER TABLE qrcodes ADD CONSTRAINT FK_B2169E7B70BEE6D FOREIGN KEY (visitor_id) REFERENCES visitors (id)');
        $this->addSql('ALTER TABLE requests ADD CONSTRAINT FK_7B85D65170BEE6D FOREIGN KEY (visitor_id) REFERENCES visitors (id)');
        $this->addSql('ALTER TABLE requests ADD CONSTRAINT FK_7B85D651A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649AE80F5DF FOREIGN KEY (department_id) REFERENCES departements (id)');
        $this->addSql('ALTER TABLE visitors ADD CONSTRAINT FK_7B74A43FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE check_ins DROP FOREIGN KEY FK_DFFFC3DF70BEE6D');
        $this->addSql('ALTER TABLE check_ins DROP FOREIGN KEY FK_DFFFC3DF12E4AD80');
        $this->addSql('ALTER TABLE evenements DROP FOREIGN KEY FK_E10AD400CCF9E01E');
        $this->addSql('ALTER TABLE qrcodes DROP FOREIGN KEY FK_B2169E7B70BEE6D');
        $this->addSql('ALTER TABLE requests DROP FOREIGN KEY FK_7B85D65170BEE6D');
        $this->addSql('ALTER TABLE requests DROP FOREIGN KEY FK_7B85D651A76ED395');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649AE80F5DF');
        $this->addSql('ALTER TABLE visitors DROP FOREIGN KEY FK_7B74A43FA76ED395');
        $this->addSql('DROP TABLE check_ins');
        $this->addSql('DROP TABLE departements');
        $this->addSql('DROP TABLE evenements');
        $this->addSql('DROP TABLE qrcodes');
        $this->addSql('DROP TABLE requests');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE visitors');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
