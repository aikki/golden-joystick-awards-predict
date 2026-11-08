<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261105191126 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE award ADD COLUMN is_default BOOLEAN NOT NULL DEFAULT 0');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__award AS SELECT id, name, close_date FROM award');
        $this->addSql('DROP TABLE award');
        $this->addSql('CREATE TABLE award (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, close_date DATETIME DEFAULT NULL)');
        $this->addSql('INSERT INTO award (id, name, close_date) SELECT id, name, close_date FROM __temp__award');
        $this->addSql('DROP TABLE __temp__award');
    }
}
