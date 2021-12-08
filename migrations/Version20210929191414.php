<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210929191414 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE billing (id INT AUTO_INCREMENT NOT NULL, id_user_id INT NOT NULL, id_car_id INT NOT NULL, start_date DATE NOT NULL, end_date DATE DEFAULT NULL, price DOUBLE PRECISION NOT NULL, paid TINYINT(1) NOT NULL, returned TINYINT(1) NOT NULL, INDEX IDX_EC224CAA79F37AE5 (id_user_id), INDEX IDX_EC224CAAE5F14372 (id_car_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE car (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, role VARCHAR(32) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE billing ADD CONSTRAINT FK_EC224CAA79F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE billing ADD CONSTRAINT FK_EC224CAAE5F14372 FOREIGN KEY (id_car_id) REFERENCES car (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE billing DROP FOREIGN KEY FK_EC224CAAE5F14372');
        $this->addSql('ALTER TABLE billing DROP FOREIGN KEY FK_EC224CAA79F37AE5');
        $this->addSql('DROP TABLE billing');
        $this->addSql('DROP TABLE car');
        $this->addSql('DROP TABLE user');
    }
}
