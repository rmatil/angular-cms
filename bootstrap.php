<?php

require_once "vendor/autoload.php";

use Doctrine\Common\Annotations\AnnotationRegistry;
use rmatil\cms\Utils\EntityManagerFactory;

// If $isDevMode is true caching is done in memory with the ArrayCache. Proxy objects are recreated on every request.
$devMode = true;

// register annotations of jms serializer
AnnotationRegistry::registerAutoloadNamespace(
    'JMS\Serializer\Annotation', __DIR__.'/vendor/jms/serializer/src'
);

$entityManager = EntityManagerFactory::createEntityManager(__DIR__.'/app/config/parameters.yml', __DIR__.'/src', $devMode);