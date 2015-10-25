<?php

namespace rmatil\cms\Controller;

use DateTime;
use Doctrine\DBAL\DBALException;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Constants\HttpStatusCodes;
use rmatil\cms\Entities\RepeatOption;
use rmatil\cms\Exceptions\EntityNotDeletedException;
use rmatil\cms\Exceptions\EntityNotFoundException;
use rmatil\cms\Exceptions\EntityNotInsertedException;
use rmatil\cms\Exceptions\EntityNotUpdatedException;
use rmatil\cms\Response\ResponseFactory;
use Slim\Http\Response;
use SlimController\SlimController;

/**
 * @package rmatil\cms\Controller
 */
class RepeatOptionController extends SlimController {

    public function getRepeatOptionsAction() {
        ResponseFactory::createJsonResponse(
            $this->app,
            $this->app
                ->dataAccessorFactory
                ->getDataAccessor(EntityNames::REPEAT_OPTION)
                ->getAll()
        );
    }

    public function getRepeatOptionByIdAction($id) {
        try {
            ResponseFactory::createJsonResponse(
                $this->app,
                $this->app
                    ->dataAccessorFactory
                    ->getDataAccessor(EntityNames::REPEAT_OPTION)
                    ->getById($id)
            );
        } catch (EntityNotFoundException $enfe) {
            ResponseFactory::createNotFoundResponse($this->app, $enfe->getMessage());
            return;
        }
    }

    public function updateRepeatOptionAction($repeatOptionId) {
        /** @var \rmatil\cms\Entities\RepeatOption $repeatOption */
        $repeatOption = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::REPEAT_OPTION, 'json');
        $repeatOption->setId($repeatOptionId);

        try {
            $repeatOption = $this->app
                ->dataAccessorFactory
                ->getDataAccessor(EntityNames::REPEAT_OPTION)
                ->update($repeatOption);
        } catch (EntityNotFoundException $enfe) {
            ResponseFactory::createNotFoundResponse($this->app, $enfe->getMessage());
            return;
        } catch (EntityNotUpdatedException $enue) {
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $enue->getMessage());
            return;
        }

        ResponseFactory::createJsonResponse($this->app, $repeatOption);
    }

    public function insertRepeatOptionAction() {
        /** @var \rmatil\cms\Entities\RepeatOption $repeatOption */
        $repeatOption = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::REPEAT_OPTION, 'json');

        try {
            $repeatOption = $this->app
                ->dataAccessorFactory
                ->getDataAccessor(EntityNames::REPEAT_OPTION)
                ->insert($repeatOption);
        } catch (EntityNotInsertedException $enie) {
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $enie->getMessage());
            return;
        }

        ResponseFactory::createJsonResponseWithCode($this->app, HttpStatusCodes::CREATED, $repeatOption);
    }

    public function deleteRepeatOptionByIdAction($id) {
        try {
            $this->app
                ->dataAccessorFactory
                ->getDataAccessor(EntityNames::REPEAT_OPTION)
                ->delete($id);
        } catch (EntityNotFoundException $enfe) {
            ResponseFactory::createNotFoundResponse($this->app, $enfe->getMessage());
            return;
        } catch (EntityNotDeletedException $ende) {
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $ende->getMessage());
            return;
        }

        $this->app->response->setStatus(HttpStatusCodes::NO_CONTENT);
    }
}