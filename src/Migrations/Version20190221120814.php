<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190221120814 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE borrowed_book');
        $this->addSql('ALTER TABLE borrowed ADD books_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE borrowed ADD CONSTRAINT FK_2F44F8E57DD8AC20 FOREIGN KEY (books_id) REFERENCES book (id)');
        $this->addSql('CREATE INDEX IDX_2F44F8E57DD8AC20 ON borrowed (books_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE borrowed_book (borrowed_id INT NOT NULL, book_id INT NOT NULL, INDEX IDX_50A9B8BC16A2B381 (book_id), INDEX IDX_50A9B8BC64BC3968 (borrowed_id), PRIMARY KEY(borrowed_id, book_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE borrowed_book ADD CONSTRAINT FK_50A9B8BC16A2B381 FOREIGN KEY (book_id) REFERENCES book (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE borrowed_book ADD CONSTRAINT FK_50A9B8BC64BC3968 FOREIGN KEY (borrowed_id) REFERENCES borrowed (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE borrowed DROP FOREIGN KEY FK_2F44F8E57DD8AC20');
        $this->addSql('DROP INDEX IDX_2F44F8E57DD8AC20 ON borrowed');
        $this->addSql('ALTER TABLE borrowed DROP books_id');
    }
}
