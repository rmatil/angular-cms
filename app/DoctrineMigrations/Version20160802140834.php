<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160802140834 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE user_group_article');
        $this->addSql('DROP TABLE user_group_event');
        $this->addSql('DROP TABLE user_group_page');
        $this->addSql('ALTER TABLE articles ADD allowed_user_group_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD3168747AC32E FOREIGN KEY (allowed_user_group_id) REFERENCES userGroups (id)');
        $this->addSql('CREATE INDEX IDX_BFDD3168747AC32E ON articles (allowed_user_group_id)');
        $this->addSql('ALTER TABLE events ADD allowed_user_group_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574A747AC32E FOREIGN KEY (allowed_user_group_id) REFERENCES userGroups (id)');
        $this->addSql('CREATE INDEX IDX_5387574A747AC32E ON events (allowed_user_group_id)');
        $this->addSql('ALTER TABLE pages ADD allowed_user_group_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE pages ADD CONSTRAINT FK_2074E575747AC32E FOREIGN KEY (allowed_user_group_id) REFERENCES userGroups (id)');
        $this->addSql('CREATE INDEX IDX_2074E575747AC32E ON pages (allowed_user_group_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_group_article (user_group_id INT NOT NULL, article_id INT NOT NULL, INDEX IDX_6AB4F73A1ED93D47 (user_group_id), INDEX IDX_6AB4F73A7294869C (article_id), PRIMARY KEY(user_group_id, article_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_group_event (user_group_id INT NOT NULL, event_id INT NOT NULL, INDEX IDX_C299E8551ED93D47 (user_group_id), INDEX IDX_C299E85571F7E88B (event_id), PRIMARY KEY(user_group_id, event_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_group_page (user_group_id INT NOT NULL, page_id INT NOT NULL, INDEX IDX_9A372BBC1ED93D47 (user_group_id), INDEX IDX_9A372BBCC4663E4 (page_id), PRIMARY KEY(user_group_id, page_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_group_article ADD CONSTRAINT FK_6AB4F73A1ED93D47 FOREIGN KEY (user_group_id) REFERENCES userGroups (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_group_article ADD CONSTRAINT FK_6AB4F73A7294869C FOREIGN KEY (article_id) REFERENCES articles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_group_event ADD CONSTRAINT FK_C299E8551ED93D47 FOREIGN KEY (user_group_id) REFERENCES userGroups (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_group_event ADD CONSTRAINT FK_C299E85571F7E88B FOREIGN KEY (event_id) REFERENCES events (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_group_page ADD CONSTRAINT FK_9A372BBC1ED93D47 FOREIGN KEY (user_group_id) REFERENCES userGroups (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_group_page ADD CONSTRAINT FK_9A372BBCC4663E4 FOREIGN KEY (page_id) REFERENCES pages (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD3168747AC32E');
        $this->addSql('DROP INDEX IDX_BFDD3168747AC32E ON articles');
        $this->addSql('ALTER TABLE articles DROP allowed_user_group_id');
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574A747AC32E');
        $this->addSql('DROP INDEX IDX_5387574A747AC32E ON events');
        $this->addSql('ALTER TABLE events DROP allowed_user_group_id');
        $this->addSql('ALTER TABLE pages DROP FOREIGN KEY FK_2074E575747AC32E');
        $this->addSql('DROP INDEX IDX_2074E575747AC32E ON pages');
        $this->addSql('ALTER TABLE pages DROP allowed_user_group_id');
    }
}
