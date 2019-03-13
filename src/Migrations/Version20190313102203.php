<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190313102203 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE borrowed DROP FOREIGN KEY FK_2F44F8E59395C3F3');
        $this->addSql('DROP INDEX IDX_2F44F8E59395C3F3 ON borrowed');
        $this->addSql('ALTER TABLE borrowed CHANGE customer_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE borrowed ADD CONSTRAINT FK_2F44F8E5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_2F44F8E5A76ED395 ON borrowed (user_id)');
        $this->addSql('ALTER TABLE user ADD has_books TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE borrowed DROP FOREIGN KEY FK_2F44F8E5A76ED395');
        $this->addSql('DROP INDEX IDX_2F44F8E5A76ED395 ON borrowed');
        $this->addSql('ALTER TABLE borrowed CHANGE user_id customer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE borrowed ADD CONSTRAINT FK_2F44F8E59395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('CREATE INDEX IDX_2F44F8E59395C3F3 ON borrowed (customer_id)');
        $this->addSql('ALTER TABLE user DROP has_books');
    }
}
