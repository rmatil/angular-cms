<?php

namespace rmatil\cms\Controller;

use DateTime;
use Doctrine\DBAL\DBALException;
use InvalidArgumentException;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Constants\HttpStatusCodes;
use rmatil\cms\Entities\File;
use rmatil\cms\Exceptions\FileAlreadyExistsException;
use rmatil\cms\Exceptions\FileNotSavedException;
use rmatil\cms\Exceptions\ThumbnailCreationFailedException;
use SlimController\SlimController;

class FileController extends SlimController {

    public function getFilesAction() {
        $entityManager      = $this->app->entityManager;
        $fileRepository     = $entityManager->getRepository(EntityNames::FILE);
        $files              = $fileRepository->findAll();

        $this->app->expires(0);
        $this->app->response->header('Content-Type', 'application/json');
        $this->app->response->setStatus(HttpStatusCodes::OK);
        $this->app->response->setBody($this->app->serializer->serialize($files, 'json'));
    }

    public function getFileByIdAction($id) {
        $entityManager      = $this->app->entityManager;
        $fileRepository     = $entityManager->getRepository(EntityNames::FILE);
        $file               = $fileRepository->findOneBy(array('id' => $id));

        if ($file === null) {
            $this->app->response->setStatus(HttpStatusCodes::NOT_FOUND);
            return;
        }

        $this->app->expires(0);
        $this->app->response->header('Content-Type', 'application/json');
        $this->app->response->setStatus(HttpStatusCodes::OK);
        $this->app->response->setBody($this->app->serializer->serialize($file, 'json'));
        
    }

    public function insertFileAction() {
        $fileObject = new File();

        try {
            $this->app->fileHandler->saveUploadedFile($fileObject);
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
        } catch (ThumbnailCreationFailedException $e) {
            // only thumbnail creation failed, file was saved
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $e->getMessage()));
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
        
        $this->app->response->header('Content-Type', 'application/json');
        $this->app->response->setStatus(HttpStatusCodes::CREATED);
        $this->app->response->setBody($this->app->serializer->serialize($fileObject, 'json'));
    }

    public function deleteFileByIdAction($id) {
        $entityManager      = $this->app->entityManager;
        $fileRepository     = $entityManager->getRepository(EntityNames::FILE);
        $file               = $fileRepository->findOneBy(array('id' => $id));

        if ($file === null) {
            $this->app->response->setStatus(HttpStatusCodes::NOT_FOUND);
            return;
        }

        try {
            $this->app->fileHandler->deleteFileOnDisk($file);
        } catch (\Exception $e) {
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $e->getMessage()));
            $this->app->response->setStatus(HttpStatusCodes::CONFLICT);
            return;
        }

        $entityManager->remove($file);

        try {
            $entityManager->flush();
        } catch (DBALException $dbalex) {
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $dbalex->getMessage()));
            $this->app->response->setStatus(HttpStatusCodes::CONFLICT);
            return;
        }

        $this->app->response->setStatus(HttpStatusCodes::NO_CONTENT);
    }
}