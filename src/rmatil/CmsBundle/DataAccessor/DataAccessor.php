<?php


namespace rmatil\CmsBundle\DataAccessor;


use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use rmatil\CmsBundle\Exception\EntityInvalidException;
use rmatil\CmsBundle\Exception\EntityNotFoundException;
use rmatil\CmsBundle\Exception\EntityNotInsertedException;
use rmatil\CmsBundle\Exception\EntityNotUpdatedException;
use rmatil\CmsBundle\Security\AclManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * A base data accessor allowing modified access to the repositories
 */
class DataAccessor implements DataAccessorInterface {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var string
     */
    protected $entityName;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * Creates a new data accessor for the given entity
     *
     * @param $entityName  string The bundle's qualifier for the entity (e.g. Bundle:Entity)
     * @param $em          EntityManagerInterface The entity manager to access the db
     * @param $aclManager  AclManager The ACL Manager used to set permissions to objects
     * @param $logger      LoggerInterface The logger
     */
    public function __construct($entityName, EntityManagerInterface $em, TokenStorageInterface $tokenStorage, LoggerInterface $logger) {
        $this->entityName = $entityName;
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function getAll() {
        return $this->em->getRepository($this->entityName)->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function getById($id) {
        $object = $this->em->getRepository($this->entityName)->find($id);

        if (null === $object) {
            throw new EntityNotFoundException(sprintf('Entity "%s" with id "%s" not found', $this->entityName, $id));
        }

        return $object;
    }

    /**
     * {@inheritdoc}
     */
    public function update($object) {
        if ( ! method_exists($object, 'getId')) {
            throw new EntityInvalidException('Object must have a method "getId()"');
        }

        if (null === $object->getId()) {
            throw new EntityInvalidException('Object identifier is missing');
        }

        $dbObject = $this->em->getRepository($this->entityName)->find($object->getId());

        if (null === $dbObject) {
            throw new EntityNotFoundException(sprintf('Entity "%s" with id "%s" not found', $this->entityName, $object->getId()));
        }

        $dbObject->update($object);

        try {
            $this->em->flush();
        } catch (DBALException $dbalex) {
            $this->logger->error($dbalex);

            throw new EntityNotUpdatedException(sprintf('Could not update entity "%s" with id "%s"', $this->entityName, $object->getId()));
        }

        return $dbObject;
    }

    /**
     * {@inheritdoc}
     */
    public function insert($object) {
        $this->em->persist($object);

        try {
            $this->em->flush();

        } catch (DBALException $dbalex) {
            $this->logger->error($dbalex);

            throw new EntityNotInsertedException(sprintf('Could not insert entity "%s" with id "%s"', get_class($object), $object->getId()));
        }

        return $object;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id) {
        $object = $this->em->getRepository($this->entityName)->find($id);

        if (null === $object) {
            throw new EntityNotFoundException(sprintf('Could not find entity "%s" with id "%s"', $this->entityName, $id));
        }

        $this->em->remove($object);
        $this->em->flush();
    }
}
