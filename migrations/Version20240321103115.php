<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240321103115 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE book_chapter (id INT AUTO_INCREMENT NOT NULL, book_id INT NOT NULL, parent_id INT DEFAULT NULL, slug VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, sort INT DEFAULT 0 NOT NULL, level INT NOT NULL, INDEX IDX_6AA19DB816A2B381 (book_id), INDEX IDX_6AA19DB8727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE book_chapter ADD CONSTRAINT FK_6AA19DB816A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE book_chapter ADD CONSTRAINT FK_6AA19DB8727ACA70 FOREIGN KEY (parent_id) REFERENCES book_chapter (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book_chapter DROP FOREIGN KEY FK_6AA19DB816A2B381');
        $this->addSql('ALTER TABLE book_chapter DROP FOREIGN KEY FK_6AA19DB8727ACA70');
        $this->addSql('DROP TABLE book_chapter');
    }
}
