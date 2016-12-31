<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161231150958 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD3168747AC32E');
        $this->addSql('DROP INDEX IDX_BFDD3168747AC32E ON articles');
        $this->addSql('ALTER TABLE articles DROP allowed_user_group_id, DROP is_published');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE articles ADD allowed_user_group_id INT DEFAULT NULL, ADD is_published TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD3168747AC32E FOREIGN KEY (allowed_user_group_id) REFERENCES userGroups (id)');
        $this->addSql('CREATE INDEX IDX_BFDD3168747AC32E ON articles (allowed_user_group_id)');
    }
}
