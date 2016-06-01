<?php


namespace rmatil\CmsBundle\DataAccessor;


use rmatil\CmsBundle\Constants\EntityNames;

class DataAccessorFactory {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var \Slim\Log
     */
    protected $logger;

    /**
     * @var \rmatil\CmsBundle\Handler\FileHandler
     */
    protected $fileHandler;

    /** @var  \rmatil\CmsBundle\Handler\RegistrationHandler */
    protected $registrationHandler;

    public function __construct($em, $logger, $fileHandler, $registrationHandler) {
        $this->em = $em;
        $this->logger = $logger;
        $this->fileHandler = $fileHandler;
        $this->registrationHandler = $registrationHandler;
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
                break;

            case EntityNames::LOCATION:
                return new LocationDataAccessor($this->em, $this->logger);
                break;

            case EntityNames::SETTING:
                return new SettingDataAccessor($this->em, $this->logger);
                break;

            case EntityNames::USER:
                return new UserDataAccessor($this->registrationHandler, $this->em, $this->logger);
                break;

            default:
                return new DataAccessor($entityName, $this->em, $this->logger);
            break;

        }
    }

}
