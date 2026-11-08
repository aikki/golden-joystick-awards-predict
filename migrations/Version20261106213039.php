<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261106213039 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__vote AS SELECT id, voter_id FROM vote');
        $this->addSql('DROP TABLE vote');
        $this->addSql('CREATE TABLE vote (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, voter_id INTEGER NOT NULL, category_id INTEGER NOT NULL, nominee_id INTEGER NOT NULL, CONSTRAINT FK_5A108564EBB4B8AD FOREIGN KEY (voter_id) REFERENCES voter (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_5A10856412469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_5A108564C6A2398 FOREIGN KEY (nominee_id) REFERENCES nominee (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO vote (id, voter_id) SELECT id, voter_id FROM __temp__vote');
        $this->addSql('DROP TABLE __temp__vote');
        $this->addSql('CREATE INDEX IDX_5A108564EBB4B8AD ON vote (voter_id)');
        $this->addSql('CREATE INDEX IDX_5A10856412469DE2 ON vote (category_id)');
        $this->addSql('CREATE INDEX IDX_5A108564C6A2398 ON vote (nominee_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__vote AS SELECT id, voter_id FROM vote');
        $this->addSql('DROP TABLE vote');
        $this->addSql('CREATE TABLE vote (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, voter_id INTEGER NOT NULL, CONSTRAINT FK_5A108564EBB4B8AD FOREIGN KEY (voter_id) REFERENCES voter (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO vote (id, voter_id) SELECT id, voter_id FROM __temp__vote');
        $this->addSql('DROP TABLE __temp__vote');
        $this->addSql('CREATE INDEX IDX_5A108564EBB4B8AD ON vote (voter_id)');
    }
}
