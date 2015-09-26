<?php


namespace rmatil\cms\DataAccessor;


use Doctrine\DBAL\DBALException;
use rmatil\cms\Exceptions\EntityInvalidException;
use rmatil\cms\Exceptions\EntityNotFoundException;
use rmatil\cms\Exceptions\EntityNotUpdatedException;
use RuntimeException;

class DataAccessor implements DataAccessorInterface {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var \Slim\Log
     */
    protected $log;

    /**
     * @var string
     */
    protected $entityName;

    public function __construct($entityName, $em, $logger) {
        $this->entityName = $entityName;
        $this->em = $em;
        $this->log = $logger;
    }

    public function getAll() {
        return $this->em->getRepository($this->entityName)->findAll();
    }

    public function getById($id) {
        $object = $this->em->getRepository($this->entityName)->find($id);

        if (null === $object) {
            throw new EntityNotFoundException(sprintf('Entity "%s" with id "%s" not found', $this->entityName, $id));
        }

        return $object;
    }

    public function update($object) {
        if ( ! method_exists($object, 'getId')) {
            throw new RuntimeException('Object must have a method "getId()"');
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
            $this->log->error($dbalex);

            throw new EntityNotUpdatedException(sprintf('Could not update entity "%s" with id "%s"', $this->entityName, $object->getId()));
        }

        return $dbObject;
    }

    public function insert($object) {
        $this->em->persist($object);

        try {
            $this->em->flush();
        } catch (DBALException $dbalex) {
            $this->log->error($dbalex);

            throw new EntityNotUpdatedException(sprintf('Could not insert entity "%s" with id "%s"', get_class($object), $object->getId()));
        }

        return $object;
    }

    public function delete($id) {
        $object = $this->em->getRepository($this->entityName)->find($id);

        if (null === $object) {
            throw new EntityNotFoundException(sprintf('Could not find entity "%s" with id "%s"', $this->entityName, $id));
        }

        $this->em->remove($object);
        $this->em->flush();
    }
}