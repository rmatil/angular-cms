<?php


namespace rmatil\CmsBundle\DataAccessor;


use DateTime;
use DateTimeZone;
use Doctrine\DBAL\DBALException;
use Exception;
use InvalidArgumentException;
use rmatil\CmsBundle\Constants\EntityNames;
use rmatil\CmsBundle\Entities\File;
use rmatil\CmsBundle\Exception\EntityInvalidException;
use rmatil\CmsBundle\Exception\EntityNotDeletedException;
use rmatil\CmsBundle\Exception\EntityNotFoundException;
use rmatil\CmsBundle\Exception\EntityNotInsertedException;
use rmatil\CmsBundle\Exception\FileAlreadyExistsException;
use rmatil\CmsBundle\Exception\FileNotSavedException;
use rmatil\CmsBundle\Exception\ThumbnailCreationFailedException;

class FileDataAccessor extends DataAccessor {

    /**
     * @var \rmatil\CmsBundle\Handler\FileHandler
     */
    protected $fileHandler;

    public function __construct($fileHandler, $em, $logger) {
        parent::__construct(EntityNames::FILE, $em, $logger);

        $this->fileHandler = $fileHandler;
    }

    public function insert($file) {
        if ( ! ($file instanceof File)) {
            throw new EntityInvalidException('Required object of type "%s" but got "%s"', EntityNames::FILE, get_class($file));
        }

        try {
            $this->fileHandler->saveUploadedFile($file);
        } catch (FileAlreadyExistsException $faee) {
            $this->logger->warn($faee);
            throw new EntityNotInsertedException($faee->getMessage());
        } catch (FileNotSavedException $fnse) {
            $this->logger->error($fnse);
            throw new EntityNotInsertedException($fnse->getMessage());
        } catch (ThumbnailCreationFailedException $tcfe) {
            $this->logger->info($tcfe);
        } catch (InvalidArgumentException $iae) {
            $this->logger->info($iae);
        }

        if ( ! $file->getDimensions()) {
            $file->setDimensions('-');
        }

        $now = new DateTime('now', new DateTimeZone('UTC'));
        $file->setCreationDate($now);

        $this->em->persist($file);

        try {
            $this->em->flush();
        } catch (DBALException $dbalex) {
            $this->logger->error($dbalex);

            throw new EntityNotInsertedException(sprintf('Could not insert entity "%s" with id "%s"', $this->entityName));
        }

        return $file;
    }

    public function delete($id) {
        $file = $this->em->getRepository(EntityNames::FILE)->find($id);

        if ( ! ($file instanceof File)) {
            throw new EntityNotFoundException(sprintf('Could not find file with id "%s"', $id));
        }

        try {
            $this->fileHandler->deleteFileOnDisk($file);
        } catch (Exception $e) {
            $this->logger->error($e);

            throw new EntityNotDeletedException($e->getMessage());
        }

        $this->em->remove($file);

        try {
            $this->em->flush();
        } catch (DBALException $dbalex) {
            $this->logger->error($dbalex);

            throw new EntityNotDeletedException($dbalex);
        }
    }
}
