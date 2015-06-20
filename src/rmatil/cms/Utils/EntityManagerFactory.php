<?php

namespace rmatil\cms\Utils;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use rmatil\cms\Handler\HandlerSingleton;
use rmatil\cms\Constants\ConfigurationNames;

abstract class EntityManagerFactory {
    
    public static function createEntityManager($httpMediaDir, $localMediaDir, $configFilePath, $sourceFolder,  $devMode) {
        $fileHandler = HandlerSingleton::getFileHandler($httpMediaDir, $localMediaDir);
        $config = $fileHandler->getConfigFileContents($configFilePath);
        
        return EntityManager::create($config[ConfigurationNames::DATABASE_PREFIX], self::getConfiguration($sourceFolder, $devMode));
    }
    
    protected static function getConfiguration($sourceFolder, $devMode) {
        // If $isDevMode is true caching is done in memory with the ArrayCache. Proxy objects are recreated on every request.
        return Setup::createAnnotationMetadataConfiguration(array($sourceFolder), $devMode, null, null, false);
    }
}

