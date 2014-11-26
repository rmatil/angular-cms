<?php

namespace rmatil\cms\Controller;

use SlimController\SlimController;
use rmatil\cms\Constants\HttpStatusCodes;
use rmatil\cms\Entities\Article;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\DBALException;
use rmatil\cms\Exceptions\FileNotSavedException;
use rmatil\cms\Exceptions\FileAlreadyExistsException;
use rmatil\cms\Exceptions\ThumbnailCreationFailedException;
use rmatil\cms\Entities\File;
use DateTime;

class FileController extends SlimController {

    private static $FILE_FULL_QUALIFIED_CLASSNAME = 'rmatil\cms\Entities\File';

    public function getFilesAction() {
        $entityManager      = $this->app->entityManager;
        $fileRepository     = $entityManager->getRepository(self::$FILE_FULL_QUALIFIED_CLASSNAME);
        $files              = $fileRepository->findAll();

        $this->app->response->header('Content-Type', 'application/json');
        $this->app->response->setStatus(HttpStatusCodes::OK);
        $this->app->response->setBody($this->app->serializer->serialize($files, 'json'));
    }

    public function getFileByIdAction($id) {
        $entityManager      = $this->app->entityManager;
        $fileRepository     = $entityManager->getRepository(self::$FILE_FULL_QUALIFIED_CLASSNAME);
        $file               = $fileRepository->findOneBy(array('id' => $id));

        if ($file === null) {
            $this->app->response->setStatus(HttpStatusCodes::NOT_FOUND);
            return;
        }

        $this->app->response->header('Content-Type', 'application/json');
        $this->app->response->setStatus(HttpStatusCodes::OK);
        $this->app->response->setBody($this->app->serializer->serialize($file, 'json'));
        
    }

    public function insertFileAction() {
        $thumbnailSaved = true;
        $fileObject = new File();

        try {
            // $this->app->fileHandler->saveUploadedFile($fileObject);
        } catch (FileAlreadyExistsException $faee) {
            $now = new DateTime();
            $this->app->log->info(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $faee->getMessage()));
            $this->app->response->setStatus(HTTP::CONFLICT);
            $this->app->response->setBody('File already exists');
            return;
        } catch (FileNotSavedException $fnse) {
            // file & thumbnail were not saved
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $fnse->getMessage()));
            $this->app->response->setStatus(HttpStatusCodes::CONFLICT);
            $this->app->response->setBody('File could not be saved');
            return;
        } catch (InvalidArgumentException $iae) {
            // only thumbnail was not saved because of wrong parameters, file was saved
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $iae->getMessage()));
            $thumbnailSaved = false;
        } catch (ThumbnailCreationFailedException $e) {
            // only thumbnail creation failed, file was saved
            $this->app->log->info($e->getMessage());
            $thumbnailSaved = false;
        }

        if (!$fileObject->getDimensions()) {
            $fileObject->setDimensions('-');
        }

        $fileObject->setDescription($this->app->request->post('description'));

        // set now as creation date
        $now                = new DateTime();
        $fileObject->setCreationDate($now);

        $entityManager      = $this->app->entityManager;
        $entityManager->persist($fileObject);

        try {
            $entityManager->flush();
        } catch(DBALException $dbalex) {
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $dbalex->getMessage()));
            $this->app->response->setStatus(HttpStatusCodes::CONFLICT);
            return;
        }

        if (!$thumbnailSaved) {
            $this->app->response->header('Content-Type', 'application/json');
            $this->app->response->setStatus(HttpStatusCodes::CONFLICT);
            $this->app->response->setBody($this->app->serializer->serialize($fileObject, 'json'));
            return;
        }
        
        $this->app->response->header('Content-Type', 'application/json');
        $this->app->response->setStatus(HttpStatusCodes::CREATED);
        $this->app->response->setBody($this->app->serializer->serialize($fileObject, 'json'));
    }

    public function deleteFileByIdAction($id) {
        $entityManager      = $this->app->entityManager;
        $fileRepository     = $entityManager->getRepository(self::$FILE_FULL_QUALIFIED_CLASSNAME);
        $file               = $fileRepository->findOneBy(array('id' => $id));

        if ($file === null) {
            $this->app->response->setStatus(HttpStatusCodes::NOT_FOUND);
            return;
        }

        $entityManager->remove($file);

        try {
            $entityManager->flush();
        } catch (DBALException $dbalex) {
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $dbalex->getMessage()));
            $this->app->response->setStatus(HttpStatusCodes::CONFLICT);
        }

        $this->app->response->setStatus(HttpStatusCodes::NO_CONTENT);
    }
}