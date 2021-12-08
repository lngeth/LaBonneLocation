<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211001174819 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE car ADD id_owner_id INT DEFAULT NULL, ADD datasheet JSON NOT NULL, ADD amount DOUBLE PRECISION NOT NULL, ADD rent VARCHAR(255) NOT NULL, ADD image VARCHAR(255) NOT NULL, ADD quantity INT NOT NULL');
        $this->addSql('ALTER TABLE car ADD CONSTRAINT FK_773DE69D2EE78D6C FOREIGN KEY (id_owner_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_773DE69D2EE78D6C ON car (id_owner_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE car DROP FOREIGN KEY FK_773DE69D2EE78D6C');
        $this->addSql('DROP INDEX IDX_773DE69D2EE78D6C ON car');
        $this->addSql('ALTER TABLE car DROP id_owner_id, DROP datasheet, DROP amount, DROP rent, DROP image, DROP quantity');
    }
}
