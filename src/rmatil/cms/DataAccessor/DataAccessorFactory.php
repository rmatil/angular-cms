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

    public function __construct($em, $logger, $fileHandler) {
        $this->em = $em;
        $this->logger = $logger;
        $this->fileHandler = $fileHandler;
    }

    public function getDataAccessor($entityName) {
        switch ($entityName) {
            case EntityNames::ARTICLE:
                return new ArticleDataAccessor($this->em, $this->logger);
                break;

            case EntityNames::PAGE:
                return new PageDataAccessor($this->em, $this->logger);
                break;

            case EntityNames::EVENT:
                return new EventDataAccessor($this->em, $this->logger);
                break;

            case EntityNames::FILE:
                return new FileDataAccessor($this->fileHandler, $this->em, $this->logger);

            case EntityNames::LOCATION:
                return new LocationDataAccessor($this->em, $this->logger);
            default:
                return new DataAccessor($entityName, $this->em, $this->logger);
            break;

        }
    }

}