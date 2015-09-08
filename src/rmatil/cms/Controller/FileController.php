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
use rmatil\cms\Response\ResponseFactory;
use Slim\Http\Response;
use SlimController\SlimController;

/**
 * @package rmatil\cms\Controller
 */
class FileController extends SlimController {

    public function getFilesAction() {
        $entityManager = $this->app->entityManager;
        $fileRepository = $entityManager->getRepository(EntityNames::FILE);
        $files = $fileRepository->findAll();

        ResponseFactory::createJsonResponse($this->app, $files);
    }

    public function getFileByIdAction($id) {
        $entityManager = $this->app->entityManager;
        $fileRepository = $entityManager->getRepository(EntityNames::FILE);
        $file = $fileRepository->findOneBy(array('id' => $id));

        if ($file === null) {
            ResponseFactory::createNotFoundResponse($this->app, 'Could not find file');
            return;
        }

        ResponseFactory::createJsonResponse($this->app, $file);

    }

    public function insertFileAction() {
        $fileObject = new File();

        try {
            /** @var \rmatil\cms\Handler\FileHandler $fh */
            $fh = $this->app->fileHandler;
            $fh->saveUploadedFile($fileObject);
        } catch (FileAlreadyExistsException $faee) {
            $now = new DateTime();
            $this->app->log->info(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $faee->getMessage()));
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, 'File already exists');
            return;
        } catch (FileNotSavedException $fnse) {
            // file & thumbnail were not saved
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $fnse->getMessage()));
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $fnse->getMessage());
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

        if ( ! $fileObject->getDimensions()) {
            $fileObject->setDimensions('-');
        }

        $fileObject->setDescription($this->app->request->post('description'));

        // set now as creation date
        $now = new DateTime();
        $fileObject->setCreationDate($now);

        $entityManager = $this->app->entityManager;
        $entityManager->persist($fileObject);

        try {
            $entityManager->flush();
        } catch (DBALException $dbalex) {
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $dbalex->getMessage()));
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $dbalex->getMessage());
            return;
        }

        ResponseFactory::createJsonResponse($this->app, $fileObject);
    }

    public function deleteFileByIdAction($id) {
        $entityManager = $this->app->entityManager;
        $fileRepository = $entityManager->getRepository(EntityNames::FILE);
        $file = $fileRepository->findOneBy(array('id' => $id));

        if ( ! ($file instanceof File)) {
            ResponseFactory::createNotFoundResponse($this->app, 'Could not find file');
            return;
        }

        try {
            $this->app->fileHandler->deleteFileOnDisk($file);
        } catch (\Exception $e) {
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $e->getMessage()));
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $e->getMessage());
            return;
        }

        $entityManager->remove($file);

        try {
            $entityManager->flush();
        } catch (DBALException $dbalex) {
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $dbalex->getMessage()));
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $dbalex->getMessage());
            return;
        }

        $this->app->response->setStatus(HttpStatusCodes::NO_CONTENT);
    }
}