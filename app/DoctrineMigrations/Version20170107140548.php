<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170107140548 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE events ADD file_id INT DEFAULT NULL, DROP file');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574A93CB796C FOREIGN KEY (file_id) REFERENCES files (id)');
        $this->addSql('CREATE INDEX IDX_5387574A93CB796C ON events (file_id)');
        $this->addSql('ALTER TABLE files ADD file VARCHAR(255) NOT NULL, DROP link, DROP local_path, DROP thumbnail_link, DROP local_thumbnail_path, DROP extension, DROP size, DROP dimensions');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574A93CB796C');
        $this->addSql('DROP INDEX IDX_5387574A93CB796C ON events');
        $this->addSql('ALTER TABLE events ADD file VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, DROP file_id');
        $this->addSql('ALTER TABLE files ADD local_path VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD thumbnail_link VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD local_thumbnail_path VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD extension VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD size INT NOT NULL, ADD dimensions VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE file link VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
    }
}
