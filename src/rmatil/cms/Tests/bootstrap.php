<?php

require_once(__DIR__."/../../../../vendor/autoload.php");

// register annotations of jms serializer and doctrine
Doctrine\Common\Annotations\AnnotationRegistry::registerAutoloadNamespace(
    'JMS\Serializer\Annotation', __DIR__.'/../../../../vendor/jms/serializer/src'
);
Doctrine\Common\Annotations\AnnotationRegistry::registerAutoloadNamespace(
    'Doctrine\Search\Mapping\Annotations', __DIR__.'/../vendor/doctrine/search/lib'
);