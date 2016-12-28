<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161228143733 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE pages DROP FOREIGN KEY FK_2074E57527166DDE');
        $this->addSql('DROP INDEX IDX_2074E57527166DDE ON pages');
        $this->addSql('ALTER TABLE pages DROP is_locked_by_id, DROP has_subnavigation');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE pages ADD is_locked_by_id INT DEFAULT NULL, ADD has_subnavigation TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE pages ADD CONSTRAINT FK_2074E57527166DDE FOREIGN KEY (is_locked_by_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_2074E57527166DDE ON pages (is_locked_by_id)');
    }
}
