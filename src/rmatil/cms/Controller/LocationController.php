<?php

namespace rmatil\cms\Controller;

use DateTime;
use Doctrine\DBAL\DBALException;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Constants\HttpStatusCodes;
use rmatil\cms\Entities\Location;
use rmatil\cms\Entities\User;
use rmatil\cms\Response\ResponseFactory;
use SlimController\SlimController;

/**
 * @package rmatil\cms\Controller
 */
class LocationController extends SlimController {

    public function getlocationsAction() {
        $entityManager = $this->app->entityManager;
        $locationRepository = $entityManager->getRepository(EntityNames::LOCATION);
        $locations = $locationRepository->findAll();

        ResponseFactory::createJsonResponse($this->app, $locations);
    }

    public function getLocationByIdAction($id) {
        $entityManager = $this->app->entityManager;
        $locationRepository = $entityManager->getRepository(EntityNames::LOCATION);
        $location = $locationRepository->findOneBy(array('id' => $id));

        if ( ! ($location instanceof Location)) {
            ResponseFactory::createNotFoundResponse($this->app, 'Could not find location');
            return;
        }

        // do not show lock if requested by the same user as currently locked
        if (($location->getIsLockedBy() instanceof User) &&
            $location->getIsLockedBy()->getId() === $_SESSION['user_id']
        ) {
            $location->setIsLockedBy(null);
        }

        $userRepository = $entityManager->getRepository(EntityNames::USER);
        $origUser = $userRepository->findOneBy(array('id' => $_SESSION['user_id']));
        $location->setAuthor($origUser);

        ResponseFactory::createJsonResponse($this->app, $location);

        // set requesting user as lock
        $location->setIsLockedBy($origUser);

        // force update
        try {
            $entityManager->flush();
        } catch (DBALException $dbalex) {
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $dbalex->getMessage()));
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $dbalex->getMessage());
            return;
        }
    }

    public function updateLocationAction($locationId) {
        /** @var \rmatil\cms\Entities\Location $locationObject */
        $locationObject = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::LOCATION, 'json');

        // get original location
        $entityManager = $this->app->entityManager;
        $locationRepository = $entityManager->getRepository(EntityNames::LOCATION);
        $origLocation = $locationRepository->findOneBy(array('id' => $locationId));

        if ( ! ($origLocation instanceof Location)) {
            ResponseFactory::createNotFoundResponse($this->app, 'Could not find location');
            return;
        }

        $userRepository = $entityManager->getRepository(EntityNames::USER);
        $origUser = $userRepository->findOneBy(array('id' => $_SESSION['user_id']));
        $locationObject->setAuthor($origUser);

        $origLocation->update($locationObject);
        // release lock on editing
        $origLocation->setIsLockedBy(null);

        // force update
        try {
            $entityManager->flush();
        } catch (DBALException $dbalex) {
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $dbalex->getMessage()));
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $dbalex->getMessage());
            return;
        }

        ResponseFactory::createJsonResponse($this->app, $origLocation);
    }

    public function insertLocationAction() {
        /** @var \rmatil\cms\Entities\Location $locationObject */
        $locationObject = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::LOCATION, 'json');

        // set now as creation date
        $now = new DateTime();
        $locationObject->setLastEditDate($now);
        $locationObject->setCreationDate($now);

        $entityManager = $this->app->entityManager;

        $userRepository = $entityManager->getRepository(EntityNames::USER);
        $origUser = $userRepository->findOneBy(array('id' => $_SESSION['user_id']));
        $locationObject->setAuthor($origUser);

        $entityManager->persist($locationObject);

        try {
            $entityManager->flush();
        } catch (DBALException $dbalex) {
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $dbalex->getMessage()));
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $dbalex->getMessage());
            return;
        }

        ResponseFactory::createJsonResponseWithCode($this->app, HttpStatusCodes::CREATED, $locationObject);
    }

    public function deleteLocationByIdAction($id) {
        $entityManager = $this->app->entityManager;
        $locationRepository = $entityManager->getRepository(EntityNames::LOCATION);
        $location = $locationRepository->findOneBy(array('id' => $id));

        if ( ! ($location instanceof Location)) {
            ResponseFactory::createNotFoundResponse($this->app, 'Could not find location');
            return;
        }

        /** @var \rmatil\cms\Entities\Event[] $attachedEvents */
        $attachedEvents = $entityManager->getRepository(EntityNames::EVENT)->findBy(array(
            'location' => $location->getId()
        ));

        foreach ($attachedEvents as $event) {
            $event->setLocation(null);
        }

        $entityManager->remove($location);

        try {
            $entityManager->flush();
        } catch (DBALException $dbalex) {
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $dbalex->getMessage()));
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $dbalex->getMessage());
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