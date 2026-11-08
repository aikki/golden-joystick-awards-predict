<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261105181348 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE award (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, close_date DATETIME DEFAULT NULL)');
        $this->addSql('CREATE TABLE category (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, award_id INTEGER NOT NULL, CONSTRAINT FK_64C19C13D5282CF FOREIGN KEY (award_id) REFERENCES award (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_64C19C13D5282CF ON category (award_id)');
        $this->addSql('CREATE TABLE nominee (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, winner BOOLEAN NOT NULL, category_id INTEGER NOT NULL, CONSTRAINT FK_FFD0B22312469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_FFD0B22312469DE2 ON nominee (category_id)');
        $this->addSql('CREATE TABLE vote (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, voter_id INTEGER NOT NULL, CONSTRAINT FK_5A108564EBB4B8AD FOREIGN KEY (voter_id) REFERENCES voter (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_5A108564EBB4B8AD ON vote (voter_id)');
        $this->addSql('CREATE TABLE voter (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, date_voted DATETIME NOT NULL, edit_token VARCHAR(255) DEFAULT NULL, award_id INTEGER NOT NULL, CONSTRAINT FK_268C4A593D5282CF FOREIGN KEY (award_id) REFERENCES award (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_268C4A593D5282CF ON voter (award_id)');
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 ON messenger_messages (queue_name, available_at, delivered_at, id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE award');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE nominee');
        $this->addSql('DROP TABLE vote');
        $this->addSql('DROP TABLE voter');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
