<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210302185600 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE subscribe (id INT AUTO_INCREMENT NOT NULL, date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event ADD subscribe_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7C72A4771 FOREIGN KEY (subscribe_id) REFERENCES subscribe (id)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA7C72A4771 ON event (subscribe_id)');
        $this->addSql('ALTER TABLE user ADD subscribe_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649C72A4771 FOREIGN KEY (subscribe_id) REFERENCES subscribe (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649C72A4771 ON user (subscribe_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7C72A4771');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649C72A4771');
        $this->addSql('DROP TABLE subscribe');
        $this->addSql('DROP INDEX IDX_3BAE0AA7C72A4771 ON event');
        $this->addSql('ALTER TABLE event DROP subscribe_id');
        $this->addSql('DROP INDEX IDX_8D93D649C72A4771 ON user');
        $this->addSql('ALTER TABLE user DROP subscribe_id');
    }
}
