<?php

namespace rmatil\cms\Handler;

use Doctrine\ORM\EntityManager;
use rmatil\cms\Handler\DatabaseHandler;
use rmatil\cms\Handler\FileHandler;
use rmatil\cms\Handler\RegistrationHandler;
use rmatil\cms\Handler\ThumbnailHandler;
use PHPMailer;

/**
 * Provides access to all handlers used by this app.
 * Guarantees the use of the same entity manager.
 */
abstract class HandlerSingleton {
    
    protected static $em;
    
    protected static $databaseHandler;
    protected static $fileHandler;
    protected static $registrationHandler;
    protected static $thumbnailHandler;
    
    public static function setEntityManager(EntityManager $em) {
        self::$em = $em;
        
        // force reinit after em changed
        self::$databaseHandler = null;
        self::$fileHandler = null;
        self::$registrationHandler = null;
        self::$thumbnailHandler = null;
    }
    
    public static function getDatabaseHandler() {
        if (null === self::$databaseHandler) {
            self::$databaseHandler = new DatabaseHandler(self::$em);
        }
        
        return self::$databaseHandler;
    }
    
    public static function getFileHandler($httpPathToMediaDir, $localPathToMediaDir) {
        if (null === self::$fileHandler) {
            self::$fileHandler = new FileHandler($httpPathToMediaDir, $localPathToMediaDir);
        }
        
        return self::$fileHandler;
    }
    
    public static function getRegistrationHandler() {
        if (null === self::$registrationHandler) {
            self::$registrationHandler = new RegistrationHandler(self::$em, new PHPMailer);
        }
        
        return self::$registrationHandler;
    }
    
    public static function getThumbnailHandler() {
        if (null === self::$thumbnailHandler) {
            self::$thumbnailHandler = new ThumbnailHandler();
        }
        
        return self::$thumbnailHandler;
    }
}
