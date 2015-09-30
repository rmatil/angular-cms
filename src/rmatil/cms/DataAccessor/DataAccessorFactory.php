<?php


namespace rmatil\cms\DataAccessor;


use rmatil\cms\Constants\EntityNames;

class DataAccessorFactory {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var \Slim\Log
     */
    protected $logger;

    public function __construct($em, $logger) {
        $this->em = $em;
        $this->logger = $logger;
    }

    public function getDataAccessor($entityName) {
        switch ($entityName) {
            case EntityNames::ARTICLE:
                return new ArticleDataAccessor($this->em, $this->logger);
                break;

            case EntityNames::EVENT:
                return new EventDataAccessor($this->em, $this->logger);
                break;

            default:
                return new DataAccessor($entityName, $this->em, $this->logger);
            break;

        }
    }

}