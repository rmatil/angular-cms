<?php

namespace rmatil\cms\Handler;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Entities\Setting;

class DatabaseHandler {
    
    protected $entityManager;
    
    protected $classes;
    
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
     * @param type $tableName The name of the table
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
        return $this->entityManager->getConnection()->getSchemaManager()->tablesExist($this->classes);
    }
    
    /**
     * Deletes the database of this project.
     */
    public function deleteDatabase() {
        $this->tool->dropDatabase();
    }
    
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
        
        $this->entityManager->persist($websiteNameSetting);
        $this->entityManager->persist($websiteEmailSetting);
        $this->entityManager->persist($websiteReplyToEmailSetting);
        $this->entityManager->persist($websiteUrlSetting);
        
        $this->entityManager->flush();
    }
    
}
