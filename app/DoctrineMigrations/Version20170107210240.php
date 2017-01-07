<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170107210240 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE files ADD last_edit_date DATETIME NOT NULL, CHANGE file file_path VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE locations DROP FOREIGN KEY FK_17E64ABA27166DDE');
        $this->addSql('ALTER TABLE locations DROP FOREIGN KEY FK_17E64ABAF675F31B');
        $this->addSql('DROP INDEX IDX_17E64ABAF675F31B ON locations');
        $this->addSql('DROP INDEX IDX_17E64ABA27166DDE ON locations');
        $this->addSql('ALTER TABLE locations DROP is_locked_by_id, DROP author_id, DROP description, DROP last_edit_date, DROP creation_date');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE files DROP last_edit_date, CHANGE file_path file VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE locations ADD is_locked_by_id INT DEFAULT NULL, ADD author_id INT DEFAULT NULL, ADD description VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD last_edit_date DATETIME NOT NULL, ADD creation_date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE locations ADD CONSTRAINT FK_17E64ABA27166DDE FOREIGN KEY (is_locked_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE locations ADD CONSTRAINT FK_17E64ABAF675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_17E64ABAF675F31B ON locations (author_id)');
        $this->addSql('CREATE INDEX IDX_17E64ABA27166DDE ON locations (is_locked_by_id)');
    }
}
