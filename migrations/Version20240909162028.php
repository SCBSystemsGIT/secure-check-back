<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240909162028 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_departments (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_departments_user (user_departments_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_C66E9EDE9A362DCC (user_departments_id), INDEX IDX_C66E9EDEA76ED395 (user_id), PRIMARY KEY(user_departments_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_departments_departements (user_departments_id INT NOT NULL, departements_id INT NOT NULL, INDEX IDX_1BFA6CDB9A362DCC (user_departments_id), INDEX IDX_1BFA6CDB1DB279A6 (departements_id), PRIMARY KEY(user_departments_id, departements_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_departments_user ADD CONSTRAINT FK_C66E9EDE9A362DCC FOREIGN KEY (user_departments_id) REFERENCES user_departments (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_departments_user ADD CONSTRAINT FK_C66E9EDEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_departments_departements ADD CONSTRAINT FK_1BFA6CDB9A362DCC FOREIGN KEY (user_departments_id) REFERENCES user_departments (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_departments_departements ADD CONSTRAINT FK_1BFA6CDB1DB279A6 FOREIGN KEY (departements_id) REFERENCES departements (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_departments_user DROP FOREIGN KEY FK_C66E9EDE9A362DCC');
        $this->addSql('ALTER TABLE user_departments_user DROP FOREIGN KEY FK_C66E9EDEA76ED395');
        $this->addSql('ALTER TABLE user_departments_departements DROP FOREIGN KEY FK_1BFA6CDB9A362DCC');
        $this->addSql('ALTER TABLE user_departments_departements DROP FOREIGN KEY FK_1BFA6CDB1DB279A6');
        $this->addSql('DROP TABLE user_departments');
        $this->addSql('DROP TABLE user_departments_user');
        $this->addSql('DROP TABLE user_departments_departements');
    }
}
