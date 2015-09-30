<?php

namespace rmatil\cms\Controller;

use DateTime;
use Doctrine\DBAL\DBALException;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Constants\HttpStatusCodes;
use rmatil\cms\Entities\Location;
use rmatil\cms\Entities\User;
use rmatil\cms\Exceptions\EntityNotDeletedException;
use rmatil\cms\Exceptions\EntityNotFoundException;
use rmatil\cms\Exceptions\EntityNotInsertedException;
use rmatil\cms\Exceptions\EntityNotUpdatedException;
use rmatil\cms\Response\ResponseFactory;
use SlimController\SlimController;

/**
 * @package rmatil\cms\Controller
 */
class LocationController extends SlimController {

    public function getlocationsAction() {
        ResponseFactory::createJsonResponse(
            $this->app,
            $this->app
                ->dataAccessorFactory
                ->getDataAccessor(EntityNames::LOCATION)
                ->getAll()
        );
    }

    public function getLocationByIdAction($id) {
        try {
            ResponseFactory::createJsonResponse(
                $this->app,
                $this->app
                    ->dataAccessorFactory
                    ->getDataAccessor(EntityNames::LOCATION)
                    ->getById($id)
            );
        } catch (EntityNotFoundException $enfe) {
            ResponseFactory::createNotFoundResponse($this->app, $enfe->getMessage());
        }
    }

    public function updateLocationAction($locationId) {
        /** @var \rmatil\cms\Entities\Location $location */
        $location = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::LOCATION, 'json');
        $location->setId($locationId);
        $location->setAuthor(
            $this->app
                ->entityManager
                ->getRepository(EntityNames::USER)
                ->find($_SESSION['user_id'])
        );

        try {
            $location = $this->app
                ->dataAccessorFactory
                ->getDataAccessor(EntityNames::LOCATION)
                ->update($location);
        } catch (EntityNotFoundException $enfe) {
            ResponseFactory::createNotFoundResponse($this->app, $enfe->getMessage());
            return;
        } catch (EntityNotUpdatedException $enue) {
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $enue->getMessage());
            return;
        }

        ResponseFactory::createJsonResponse($this->app, $location);
    }

    public function insertLocationAction() {
        /** @var \rmatil\cms\Entities\Location $location */
        $location = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::LOCATION, 'json');
        $location->setAuthor(
            $this->app
                ->entityManager
                ->getRepository(EntityNames::USER)
                ->find($_SESSION['user_id'])
        );

        try {
            $location = $this->app
                ->dataAccessorFactory
                ->getDataAccessor(EntityNames::LOCATION)
                ->insert($location);
        } catch (EntityNotInsertedException $enie) {
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $enie->getMessage());
            return;
        }

        ResponseFactory::createJsonResponseWithCode($this->app, HttpStatusCodes::CREATED, $location);
    }

    public function deleteLocationByIdAction($id) {
        try {
            $this->app
                ->dataAccessorFactory
                ->getDataAccessor(EntityNames::LOCATION)
                ->delete($id);
        } catch (EntityNotFoundException $enfe ) {
            ResponseFactory::createNotFoundResponse($this->app, $enfe->getMessage());
            return;
        } catch (EntityNotDeletedException $ende) {
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $ende->getMessage());
            return;
        }

        $this->app->response->setStatus(HttpStatusCodes::NO_CONTENT);
    }

    public function getEmptyLocationAction() {
        $location = new Location();

        $entityManager = $this->app->entityManager;
        $now = new DateTime();

        $userRepository = $entityManager->getRepository(EntityNames::USER);
        $origUser = $userRepository->findOneBy(array('id' => $_SESSION['user_id']));
        $location->setAuthor($origUser);

        $location->setCreationDate($now);
        $location->setLastEditDate($now);

        ResponseFactory::createJsonResponse($this->app, $location);
    }
}