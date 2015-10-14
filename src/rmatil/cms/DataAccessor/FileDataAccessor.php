<?php


namespace rmatil\cms\DataAccessor;


use DateTime;
use DateTimeZone;
use Doctrine\DBAL\DBALException;
use Exception;
use InvalidArgumentException;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Entities\File;
use rmatil\cms\Exceptions\EntityInvalidException;
use rmatil\cms\Exceptions\EntityNotDeletedException;
use rmatil\cms\Exceptions\EntityNotFoundException;
use rmatil\cms\Exceptions\EntityNotInsertedException;
use rmatil\cms\Exceptions\FileAlreadyExistsException;
use rmatil\cms\Exceptions\FileNotSavedException;
use rmatil\cms\Exceptions\ThumbnailCreationFailedException;

class FileDataAccessor extends DataAccessor {

    /**
     * @var \rmatil\cms\Handler\FileHandler
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
            $this->log->warn($faee);
            throw new EntityNotInsertedException($faee->getMessage());
        } catch (FileNotSavedException $fnse) {
            $this->log->error($fnse);
            throw new EntityNotInsertedException($fnse->getMessage());
        } catch (ThumbnailCreationFailedException $tcfe) {
            $this->log->info($tcfe);
        } catch (InvalidArgumentException $iae) {
            $this->log->info($iae);
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
            $this->log->error($dbalex);

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
            $this->log->error($e);

            throw new EntityNotDeletedException($e->getMessage());
        }

        $this->em->remove($file);

        try {
            $this->em->flush();
        } catch (DBALException $dbalex) {
            $this->log->error($dbalex);

            throw new EntityNotDeletedException($dbalex);
        }
    }
}