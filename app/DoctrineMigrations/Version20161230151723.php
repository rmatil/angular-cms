<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161230151723 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE articles (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, author_id INT DEFAULT NULL, language_id INT DEFAULT NULL, page_id INT DEFAULT NULL, allowed_user_group_id INT DEFAULT NULL, url_name VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT DEFAULT NULL, last_edit_date DATETIME NOT NULL, creation_date DATETIME NOT NULL, is_published TINYINT(1) NOT NULL, INDEX IDX_BFDD316812469DE2 (category_id), INDEX IDX_BFDD3168F675F31B (author_id), INDEX IDX_BFDD316882F1BAF4 (language_id), INDEX IDX_BFDD3168C4663E4 (page_id), INDEX IDX_BFDD3168747AC32E (allowed_user_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE articleCategories (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE events (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, location_id INT DEFAULT NULL, file_id INT DEFAULT NULL, repeat_option_id INT DEFAULT NULL, is_locked_by_id INT DEFAULT NULL, allowed_user_group_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, start_date DATETIME DEFAULT NULL, end_date DATETIME DEFAULT NULL, description LONGTEXT DEFAULT NULL, last_edit_date DATETIME NOT NULL, creation_date DATETIME NOT NULL, url_name VARCHAR(255) NOT NULL, INDEX IDX_5387574AF675F31B (author_id), INDEX IDX_5387574A64D218E (location_id), INDEX IDX_5387574A93CB796C (file_id), INDEX IDX_5387574AC8A9295D (repeat_option_id), INDEX IDX_5387574A27166DDE (is_locked_by_id), INDEX IDX_5387574A747AC32E (allowed_user_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE files (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, link VARCHAR(255) NOT NULL, local_path VARCHAR(255) NOT NULL, thumbnail_link VARCHAR(255) DEFAULT NULL, local_thumbnail_path VARCHAR(255) DEFAULT NULL, extension VARCHAR(255) NOT NULL, size INT NOT NULL, dimensions VARCHAR(255) NOT NULL, creation_date DATETIME NOT NULL, INDEX IDX_6354059F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE languages (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE locations (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, is_locked_by_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, longitude DOUBLE PRECISION NOT NULL, latitude DOUBLE PRECISION NOT NULL, last_edit_date DATETIME NOT NULL, creation_date DATETIME NOT NULL, INDEX IDX_17E64ABAF675F31B (author_id), INDEX IDX_17E64ABA27166DDE (is_locked_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pages (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, language_id INT DEFAULT NULL, parent_id INT DEFAULT NULL, allowed_user_group_id INT DEFAULT NULL, url_name VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, is_published TINYINT(1) NOT NULL, last_edit_date DATETIME NOT NULL, creation_date DATETIME NOT NULL, is_start_page TINYINT(1) NOT NULL, INDEX IDX_2074E575F675F31B (author_id), INDEX IDX_2074E57582F1BAF4 (language_id), INDEX IDX_2074E575727ACA70 (parent_id), INDEX IDX_2074E575747AC32E (allowed_user_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE registrations (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, expiration_date DATETIME NOT NULL, token VARCHAR(255) NOT NULL, INDEX IDX_53DE51E7A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE repeatOptions (id INT AUTO_INCREMENT NOT NULL, option_value VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE settings (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, value VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, phone_number VARCHAR(255) DEFAULT NULL, mobile_number VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, zip_code VARCHAR(255) DEFAULT NULL, place VARCHAR(255) DEFAULT NULL, locked TINYINT(1) NOT NULL, username_canonical VARCHAR(191) NOT NULL, email_canonical VARCHAR(191) NOT NULL, UNIQUE INDEX UNIQ_1483A5E9C05FB297 (confirmation_token), UNIQUE INDEX UNIQ_1483A5E992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_1483A5E9A0D96FBF (email_canonical), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE userGroups (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, role VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD316812469DE2 FOREIGN KEY (category_id) REFERENCES articleCategories (id)');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD3168F675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD316882F1BAF4 FOREIGN KEY (language_id) REFERENCES languages (id)');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD3168C4663E4 FOREIGN KEY (page_id) REFERENCES pages (id)');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD3168747AC32E FOREIGN KEY (allowed_user_group_id) REFERENCES userGroups (id)');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574AF675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574A64D218E FOREIGN KEY (location_id) REFERENCES locations (id)');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574A93CB796C FOREIGN KEY (file_id) REFERENCES files (id)');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574AC8A9295D FOREIGN KEY (repeat_option_id) REFERENCES repeatOptions (id)');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574A27166DDE FOREIGN KEY (is_locked_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574A747AC32E FOREIGN KEY (allowed_user_group_id) REFERENCES userGroups (id)');
        $this->addSql('ALTER TABLE files ADD CONSTRAINT FK_6354059F675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE locations ADD CONSTRAINT FK_17E64ABAF675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE locations ADD CONSTRAINT FK_17E64ABA27166DDE FOREIGN KEY (is_locked_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE pages ADD CONSTRAINT FK_2074E575F675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE pages ADD CONSTRAINT FK_2074E57582F1BAF4 FOREIGN KEY (language_id) REFERENCES languages (id)');
        $this->addSql('ALTER TABLE pages ADD CONSTRAINT FK_2074E575727ACA70 FOREIGN KEY (parent_id) REFERENCES pages (id)');
        $this->addSql('ALTER TABLE pages ADD CONSTRAINT FK_2074E575747AC32E FOREIGN KEY (allowed_user_group_id) REFERENCES userGroups (id)');
        $this->addSql('ALTER TABLE registrations ADD CONSTRAINT FK_53DE51E7A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD316812469DE2');
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574A93CB796C');
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD316882F1BAF4');
        $this->addSql('ALTER TABLE pages DROP FOREIGN KEY FK_2074E57582F1BAF4');
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574A64D218E');
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD3168C4663E4');
        $this->addSql('ALTER TABLE pages DROP FOREIGN KEY FK_2074E575727ACA70');
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574AC8A9295D');
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD3168F675F31B');
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574AF675F31B');
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574A27166DDE');
        $this->addSql('ALTER TABLE files DROP FOREIGN KEY FK_6354059F675F31B');
        $this->addSql('ALTER TABLE locations DROP FOREIGN KEY FK_17E64ABAF675F31B');
        $this->addSql('ALTER TABLE locations DROP FOREIGN KEY FK_17E64ABA27166DDE');
        $this->addSql('ALTER TABLE pages DROP FOREIGN KEY FK_2074E575F675F31B');
        $this->addSql('ALTER TABLE registrations DROP FOREIGN KEY FK_53DE51E7A76ED395');
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD3168747AC32E');
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574A747AC32E');
        $this->addSql('ALTER TABLE pages DROP FOREIGN KEY FK_2074E575747AC32E');
        $this->addSql('DROP TABLE articles');
        $this->addSql('DROP TABLE articleCategories');
        $this->addSql('DROP TABLE events');
        $this->addSql('DROP TABLE files');
        $this->addSql('DROP TABLE languages');
        $this->addSql('DROP TABLE locations');
        $this->addSql('DROP TABLE pages');
        $this->addSql('DROP TABLE registrations');
        $this->addSql('DROP TABLE repeatOptions');
        $this->addSql('DROP TABLE settings');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE userGroups');
    }
}
