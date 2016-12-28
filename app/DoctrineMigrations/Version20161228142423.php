<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161228142423 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE pages DROP FOREIGN KEY FK_2074E57512469DE2');
        $this->addSql('DROP TABLE pageCategories');
        $this->addSql('DROP INDEX IDX_2074E57512469DE2 ON pages');
        $this->addSql('ALTER TABLE pages DROP category_id');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE pageCategories (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pages ADD category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE pages ADD CONSTRAINT FK_2074E57512469DE2 FOREIGN KEY (category_id) REFERENCES pageCategories (id)');
        $this->addSql('CREATE INDEX IDX_2074E57512469DE2 ON pages (category_id)');
    }
}
