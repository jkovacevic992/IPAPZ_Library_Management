<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190218111557 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE genre (id INT AUTO_INCREMENT NOT NULL, name LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, email VARCHAR(180) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE employee (id INT AUTO_INCREMENT NOT NULL, first_name LONGTEXT NOT NULL, last_name LONGTEXT NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, UNIQUE INDEX UNIQ_5D9F75A1E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book (id INT AUTO_INCREMENT NOT NULL, employee_id INT DEFAULT NULL, genre_id INT DEFAULT NULL, name LONGTEXT NOT NULL, author LONGTEXT NOT NULL, INDEX IDX_CBE5A3318C03F15C (employee_id), INDEX IDX_CBE5A3314296D31F (genre_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE borrowed (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, borrow_date DATETIME NOT NULL, return_date DATETIME NOT NULL, INDEX IDX_2F44F8E5A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE books_borrowed (borrowed_id INT NOT NULL, book_id INT NOT NULL, INDEX IDX_464B489D64BC3968 (borrowed_id), INDEX IDX_464B489D16A2B381 (book_id), PRIMARY KEY(borrowed_id, book_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A3318C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id)');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A3314296D31F FOREIGN KEY (genre_id) REFERENCES genre (id)');
        $this->addSql('ALTER TABLE borrowed ADD CONSTRAINT FK_2F44F8E5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE books_borrowed ADD CONSTRAINT FK_464B489D64BC3968 FOREIGN KEY (borrowed_id) REFERENCES borrowed (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE books_borrowed ADD CONSTRAINT FK_464B489D16A2B381 FOREIGN KEY (book_id) REFERENCES book (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A3314296D31F');
        $this->addSql('ALTER TABLE borrowed DROP FOREIGN KEY FK_2F44F8E5A76ED395');
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A3318C03F15C');
        $this->addSql('ALTER TABLE books_borrowed DROP FOREIGN KEY FK_464B489D16A2B381');
        $this->addSql('ALTER TABLE books_borrowed DROP FOREIGN KEY FK_464B489D64BC3968');
        $this->addSql('DROP TABLE genre');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE employee');
        $this->addSql('DROP TABLE book');
        $this->addSql('DROP TABLE borrowed');
        $this->addSql('DROP TABLE books_borrowed');
    }
}
