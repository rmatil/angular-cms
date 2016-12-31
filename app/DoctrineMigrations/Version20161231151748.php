<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161231151748 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574A27166DDE');
        $this->addSql('DROP INDEX IDX_5387574A27166DDE ON events');
        $this->addSql('ALTER TABLE events ADD additional_info LONGTEXT DEFAULT NULL, DROP is_locked_by_id, CHANGE description content LONGTEXT DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE events ADD is_locked_by_id INT DEFAULT NULL, ADD description LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, DROP content, DROP additional_info');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574A27166DDE FOREIGN KEY (is_locked_by_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_5387574A27166DDE ON events (is_locked_by_id)');
    }
}
