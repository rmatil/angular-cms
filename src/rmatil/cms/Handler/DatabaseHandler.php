<?php

namespace rmatil\cms\Handler;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Entities\ArticleCategory;
use rmatil\cms\Entities\PageCategory;
use rmatil\cms\Entities\Setting;
use rmatil\cms\Entities\UserGroup;

class DatabaseHandler {

    /**
     * @var $entityManager EntityManager
     */
    protected $entityManager;

    /**
     * @var $classes \Doctrine\ORM\Mapping\ClassMetadata[]
     */
    protected $classes;

    /**
     * @var $tool SchemaTool
     */
    protected $tool;
    
    public function __construct(EntityManager $entityManager) {
        $this->entityManager = $entityManager;
        $this->tool = new SchemaTool($this->entityManager);
        $this->classes = array(
            $this->entityManager->getClassMetadata(EntityNames::ARTICLE),
            $this->entityManager->getClassMetadata(EntityNames::ARTICLE_CATEGORY),
            $this->entityManager->getClassMetadata(EntityNames::EVENT),
            $this->entityManager->getClassMetadata(EntityNames::FILE),
            $this->entityManager->getClassMetadata(EntityNames::FILE_CATEGORY),
            $this->entityManager->getClassMetadata(EntityNames::LANGUAGE),
            $this->entityManager->getClassMetadata(EntityNames::LOCATION),
            $this->entityManager->getClassMetadata(EntityNames::PAGE),
            $this->entityManager->getClassMetadata(EntityNames::PAGE_CATEGORY),
            $this->entityManager->getClassMetadata(EntityNames::REGISTRATION),
            $this->entityManager->getClassMetadata(EntityNames::REPEAT_OPTION),
            $this->entityManager->getClassMetadata(EntityNames::SETTING),
            $this->entityManager->getClassMetadata(EntityNames::USER),
            $this->entityManager->getClassMetadata(EntityNames::USER_GROUP)
        );
    }
    
    /**
     * Creates all tables needed by this project
     */
    public function setupDatabase() {
        $this->tool->createSchema($this->classes, true);
    }
    
    /**
     * Checks whether the table already exists in the database
     * 
     * @param string $tableName The name of the table
     * @return bool true|false True if the table exists
     */
    public function tableExists($tableName) {
        return $this->entityManager->getConnection()->getSchemaManager()->tablesExist(array($tableName));
    }
    
    /**
     * Checks if all tables are generated 
     * 
     * @return bool true|false True if all tables were generated, otherwise false
     */
    public function schemaExists() {
        $tableNames = array();
        foreach ($this->classes as $class) {
            $tableNames[] = $class->getTableName();
        }

        return $this->entityManager->getConnection()->getSchemaManager()->tablesExist($tableNames);
    }
    
    /**
     * Deletes the database of this project.
     */
    public function deleteDatabase() {
        $this->tool->dropDatabase();
    }

    /**
     * Initalises the default settings used for this application
     *
     * @param $websiteName The name of the website
     * @param $websiteEmail The email of the website
     * @param $websiteReplyToEmail The email to which users may reply
     * @param $websiteUrl The url of the website
     */
    public function initDatabaseSettings($websiteName, $websiteEmail, $websiteReplyToEmail, $websiteUrl) {
        $websiteNameSetting = new Setting();
        $websiteNameSetting->setName('website_name');
        $websiteNameSetting->setValue($websiteName);
        
        $websiteEmailSetting = new Setting();
        $websiteEmailSetting->setName('website_email');
        $websiteEmailSetting->setValue($websiteEmail);
        
        $websiteReplyToEmailSetting = new Setting();
        $websiteReplyToEmailSetting->setName('website_reply_to_email');
        $websiteReplyToEmailSetting->setValue($websiteReplyToEmail);
        
        $websiteUrlSetting = new Setting();
        $websiteUrlSetting->setName('website_url');
        $websiteUrlSetting->setValue($websiteUrl);

        $debugMode = new Setting();
        $debugMode->setName('debug_mode');
        $debugMode->setValue(false);

        $defaultArticleCategory = new ArticleCategory();
        $defaultArticleCategory->setName('Default');

        $defaultPageCategory = new PageCategory();
        $defaultPageCategory->setName('Default');

        $defaultUserGroup = new UserGroup();
        $defaultUserGroup->setName('User');
        $defaultUserGroup->setRole('ROLE_USER');
        
        $this->entityManager->persist($websiteNameSetting);
        $this->entityManager->persist($websiteEmailSetting);
        $this->entityManager->persist($websiteReplyToEmailSetting);
        $this->entityManager->persist($websiteUrlSetting);
        $this->entityManager->persist($debugMode);
        $this->entityManager->persist($defaultArticleCategory);
        $this->entityManager->persist($defaultPageCategory);
        $this->entityManager->persist($defaultUserGroup);
        
        $this->entityManager->flush();
    }
    
}
