<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190221163452 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE borrowed_books (id INT AUTO_INCREMENT NOT NULL, book_id INT DEFAULT NULL, borrowed_id INT DEFAULT NULL, INDEX IDX_D989991416A2B381 (book_id), INDEX IDX_D989991464BC3968 (borrowed_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE borrowed_books ADD CONSTRAINT FK_D989991416A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE borrowed_books ADD CONSTRAINT FK_D989991464BC3968 FOREIGN KEY (borrowed_id) REFERENCES borrowed (id)');
        $this->addSql('DROP TABLE borrowed_book');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE borrowed_book (borrowed_id INT NOT NULL, book_id INT NOT NULL, INDEX IDX_50A9B8BC16A2B381 (book_id), INDEX IDX_50A9B8BC64BC3968 (borrowed_id), PRIMARY KEY(borrowed_id, book_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE borrowed_book ADD CONSTRAINT FK_50A9B8BC16A2B381 FOREIGN KEY (book_id) REFERENCES book (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE borrowed_book ADD CONSTRAINT FK_50A9B8BC64BC3968 FOREIGN KEY (borrowed_id) REFERENCES borrowed (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE borrowed_books');
    }
}
