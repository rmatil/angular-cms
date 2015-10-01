<?php

namespace rmatil\cms\Controller;

use DateTime;
use Doctrine\DBAL\DBALException;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Constants\HttpStatusCodes;
use rmatil\cms\Entities\RepeatOption;
use rmatil\cms\Entities\Setting;
use rmatil\cms\Exceptions\EntityInvalidException;
use rmatil\cms\Exceptions\EntityNotDeletedException;
use rmatil\cms\Exceptions\EntityNotFoundException;
use rmatil\cms\Exceptions\EntityNotUpdatedException;
use rmatil\cms\Response\ResponseFactory;
use Slim\Http\Response;
use SlimController\SlimController;

/**
 * @package rmatil\cms\Controller
 */
class SettingController extends SlimController {

    public function getSettingsAction() {
        ResponseFactory::createJsonResponse(
            $this->app,
            $this->app
                ->dataAccessorFactory
                ->getDataAccessor(EntityNames::SETTING)
                ->getAll()
        );
    }

    public function getSettingByIdAction($id) {
        try {
            ResponseFactory::createJsonResponse(
                $this->app,
                $this->app
                    ->dataAccessorFactory
                    ->getDataAccessor(EntityNames::SETTING)
                    ->getById($id)
            );
        } catch (EntityNotFoundException $enfe) {
            ResponseFactory::createNotFoundResponse($this->app, $enfe->getMessage());
            return;
        }
    }

    public function postSettingsAction() {
        /** @var \rmatil\cms\Entities\Setting $setting */
        $setting = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::SETTING, 'json');

        try {
            $setting = $this->app
                ->dataAccessorFactory
                ->getDataAccessor(EntityNames::SETTING)
                ->insert($setting);
        } catch (EntityInvalidException $eie) {
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $eie->getMessage());
            return;
        }

        ResponseFactory::createJsonResponse($this->app, $setting);
    }

    public function updateSettingsAction($id) {
        /** @var \rmatil\cms\Entities\Setting $setting */
        $setting = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::SETTING, 'json');

        try {
            $setting = $this->app
                ->dataAccessorFactory
                ->getDataAccessor(EntityNames::SETTING)
                ->update($setting);
        } catch (EntityNotFoundException $enfe) {
            ResponseFactory::createNotFoundResponse($this->app, $enfe->getMessage());
            return;
        } catch (EntityNotUpdatedException $enue) {
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $enue->getMessage());
            return;
        }

        ResponseFactory::createJsonResponse($this->app, $setting);
    }

    public function deleteSettingAction($id) {
        try {
            $this->app
                ->dataAccessorFactory
                ->getDataAccessor(EntityNames::SETTING)
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