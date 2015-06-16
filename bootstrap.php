<?php

require_once "vendor/autoload.php";

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\Yaml\Yaml;

// If $isDevMode is true caching is done in memory with the ArrayCache. Proxy objects are recreated on every request.
$isDevMode = true;
$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/src"), $isDevMode, null, null, false); // false to use @ORM\Annotaion

// register annotations of jms serializer
AnnotationRegistry::registerAutoloadNamespace(
    'JMS\Serializer\Annotation', __DIR__.'/vendor/jms/serializer/src'
);


$params = Yaml::parse(file_get_contents(__DIR__.'/config/yaml/parameters.yml'));

// the connection configuration
$dbParams = array(
    'driver'   => $params['database']['driver'],
    'user'     => $params['database']['username'],
    'password' => $params['database']['password'],
    'dbname'   => $params['database']['dbname'],
    'host'     => $params['database']['host'],
    // http://php.net/manual/en/ref.pdo-mysql.php#pdo.constants.mysql-attr-init-command
    'driverOptions' => array(1002 => 'SET NAMES utf8')
);

$mailParams = array(
    'CharSet'  => strtoupper($params['mail']['char_set']),
    'Host'     => $params['mail']['host'],
    'SMTPAuth' => $params['mail']['smtp_auth'],
    'Username' => $params['mail']['username'],
    'Password' => $params['mail']['password'],
    'Port'     => $params['mail']['port']
);

$entityManager       = EntityManager::create($dbParams, $config);