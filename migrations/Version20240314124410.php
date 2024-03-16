<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240314124410 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A33197CD605C');
        $this->addSql('DROP INDEX IDX_CBE5A33197CD605C ON book');
        $this->addSql('ALTER TABLE book DROP formats_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book ADD formats_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A33197CD605C FOREIGN KEY (formats_id) REFERENCES book_to_book_format (id)');
        $this->addSql('CREATE INDEX IDX_CBE5A33197CD605C ON book (formats_id)');
    }
}
