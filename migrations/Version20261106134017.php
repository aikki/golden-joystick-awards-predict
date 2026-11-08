<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261106134017 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE voter ADD COLUMN signature_font VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__voter AS SELECT id, name, date_voted, edit_token, award_id FROM voter');
        $this->addSql('DROP TABLE voter');
        $this->addSql('CREATE TABLE voter (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, date_voted DATETIME NOT NULL, edit_token VARCHAR(255) DEFAULT NULL, award_id INTEGER NOT NULL, CONSTRAINT FK_268C4A593D5282CF FOREIGN KEY (award_id) REFERENCES award (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO voter (id, name, date_voted, edit_token, award_id) SELECT id, name, date_voted, edit_token, award_id FROM __temp__voter');
        $this->addSql('DROP TABLE __temp__voter');
        $this->addSql('CREATE INDEX IDX_268C4A593D5282CF ON voter (award_id)');
    }
}
