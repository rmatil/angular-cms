<?php

namespace rmatil\cms\Controller;

use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Constants\HttpStatusCodes;
use rmatil\cms\Entities\File;
use rmatil\cms\Exceptions\EntityNotDeletedException;
use rmatil\cms\Exceptions\EntityNotFoundException;
use rmatil\cms\Exceptions\EntityNotInsertedException;
use rmatil\cms\Response\ResponseFactory;
use SlimController\SlimController;

/**
 * @package rmatil\cms\Controller
 */
class FileController extends SlimController {

    public function getFilesAction() {
        ResponseFactory::createJsonResponse(
            $this->app,
            $this->app
                ->dataAccessorFactory
                ->getDataAccessor(EntityNames::FILE)
                ->getAll()
        );
    }

    public function getFileByIdAction($id) {
        try {
            ResponseFactory::createJsonResponse(
                $this->app,
                $this->app
                    ->dataAccessorFactory
                    ->getDataAccessor(EntityNames::FILE)
                    ->getById($id)
            );
        } catch (EntityNotFoundException $enfe) {
            ResponseFactory::createNotFoundResponse($this->app, $enfe->getMessage());
        }
    }

    public function insertFileAction() {
        $file = new File();
        $file->setDescription(
            $this->app->request->post('description')
        );

        try {
            $file = $this->app
                ->dataAccessorFactory
                ->getDataAccessor(EntityNames::FILE)
                ->insert($file);
        } catch (EntityNotInsertedException $enie) {
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $enie->getMessage());
            return;
        }

        ResponseFactory::createJsonResponse($this->app, $file);
    }

    public function deleteFileByIdAction($id) {
        try {
            $this->app
                ->dataAccessorFactory
                ->getDataAccessor(EntityNames::FILE)
                ->delete($id);
        } catch (EntityNotDeletedException $ende) {
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $ende->getMessage());
            return;
        }

        $this->app->response->setStatus(HttpStatusCodes::NO_CONTENT);
    }
}