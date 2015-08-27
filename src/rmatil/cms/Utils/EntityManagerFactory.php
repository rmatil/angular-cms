<?php

namespace rmatil\cms\Utils;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use rmatil\cms\Handler\ConfigurationHandler;
use rmatil\cms\Handler\HandlerSingleton;
use rmatil\cms\Constants\ConfigurationNames;

/**
 * Creates an EntityManager based on the configuration file
 * @package rmatil\cms\Utils
 */
abstract class EntityManagerFactory {

    public static function createEntityManager($configFilePath, $sourceFolder,  $devMode) {
        $config = ConfigurationHandler::readConfiguration($configFilePath);
        
        return EntityManager::create($config[ConfigurationNames::DATABASE_PREFIX], self::getConfiguration($sourceFolder, $devMode));
    }
    
    protected static function getConfiguration($sourceFolder, $devMode) {
        // If $isDevMode is true caching is done in memory with the ArrayCache. Proxy objects are recreated on every request.
        return Setup::createAnnotationMetadataConfiguration(array($sourceFolder), $devMode, null, null, false);
    }
}

