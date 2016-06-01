<?php


namespace rmatil\CmsBundle\Serializer;


use JMS\Serializer\Naming\CamelCaseNamingStrategy;
use JMS\Serializer\SerializerBuilder;

class Serializer extends \JMS\Serializer\Serializer {

    protected $serializer;

    public function __construct() {
        $namingStrategy = new CamelCaseNamingStrategy();

        $this->serializer = SerializerBuilder::create()
            ->addDefaultDeserializationVisitors()
            ->addDefaultSerializationVisitors()
            ->setSerializationVisitor('json', new JsonSerializationVisitor($namingStrategy))
            ->build();
    }
    
}
