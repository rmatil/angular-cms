<?php

namespace rmatil\cms\Controller;

use SlimController\SlimController;
use rmatil\cms\Constants\HttpStatusCodes;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Entities\Article;
use rmatil\cms\Entities\Event;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\DBALException;
use DateTime;

class EventController extends SlimController {

    public function getEventsAction() {
        $entityManager      = $this->app->entityManager;
        $eventRepository    = $entityManager->getRepository(EntityNames::EVENT);
        $events             = $eventRepository->findAll();

        $this->app->expires(0);
        $this->app->response->header('Content-Type', 'application/json');
        $this->app->response->setStatus(HttpStatusCodes::OK);
        $this->app->response->setBody($this->app->serializer->serialize($events, 'json'));
    }

    public function getEventByIdAction($id) {
        $entityManager      = $this->app->entityManager;
        $eventRepository    = $entityManager->getRepository(EntityNames::EVENT);
        $event              = $eventRepository->findOneBy(array('id' => $id));

        if ($event === null) {
            $this->app->response->setStatus(HttpStatusCodes::NOT_FOUND);
            return;
        }

        // do not show lock if requested by the same user as currently locked
        if ($event->getIsLockedBy() !== null &&
            $event->getIsLockedBy()->getId() === $_SESSION['user_id']) {
            $event->setIsLockedBy(null);
        }

        $userRepository = $this->app->entityManager->getRepository(EntityNames::USER);
        $origUser       = $userRepository->findOneBy(array('id' => $_SESSION['user_id']));
        $event->setAuthor($origUser);

        $this->app->expires(0);
        $this->app->response->header('Content-Type', 'application/json');
        $this->app->response->setStatus(HttpStatusCodes::OK);
        $this->app->response->setBody($this->app->serializer->serialize($event, 'json'));       

         // set requesting user as lock
        $event->setIsLockedBy($origUser);

        // force update
        try {
            $entityManager->flush();
        } catch (DBALException $dbalex) {
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $dbalex->getMessage()));
            $this->app->response->setStatus(HttpStatusCodes::CONFLICT);
            return;
        } 
    }

    public function updateEventAction($eventId) {
        $eventObject        = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::EVENT, 'json');

        // get original event
        $entityManager      = $this->app->entityManager;
        $eventRepository    = $entityManager->getRepository(EntityNames::EVENT);
        $origEvent          = $eventRepository->findOneBy(array('id' => $eventId));

        $userRepository     = $entityManager->getRepository(EntityNames::USER);
        $origUser           = $userRepository->findOneBy(array('id' => $_SESSION['user_id']));
        $eventObject->setAuthor($origUser);

        $locationRepository = $entityManager->getRepository(EntityNames::LOCATION);
        $origLocation       = $locationRepository->findOneBy(array('id' => $eventObject->getLocation()->getId()));
        $eventObject->setLocation($origLocation);

        $repeatOptionRepository = $entityManager->getRepository(EntityNames::REPEAT_OPTION);
        $origRepeatOption   = $repeatOptionRepository->findOneBy(array('id' => $eventObject->getRepeatOption()->getId()));
        $eventObject->setRepeatOption($origRepeatOption);

        $fileRepository     = $entityManager->getRepository(EntityNames::FILE);
        $origFile           = $fileRepository->findOneBy(array('id' => $eventObject->getFile()));
        $eventObject->setFile($origFile);

        $origEvent->update($eventObject);
        // release lock on editing
        $origEvent->setIsLockedBy(null);

        // force update
        try {
            $entityManager->flush();
        } catch (DBALException $dbalex) {
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $dbalex->getMessage()));
            $this->app->response->setStatus(HttpStatusCodes::CONFLICT);
            return;
        }

        $this->app->expires(0);
        $this->app->response->header('Content-Type', 'application/json');
        $this->app->response->setStatus(HttpStatusCodes::OK);
        $this->app->response->setBody($this->app->serializer->serialize($origEvent, 'json'));
    }

    public function insertEventAction() {
        $eventObject      = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::EVENT, 'json');

        // set now as creation date
        $now                = new DateTime();
        $eventObject->setLastEditDate($now);
        $eventObject->setCreationDate($now);

        $entityManager      = $this->app->entityManager;

        $userRepository     = $entityManager->getRepository(EntityNames::USER);
        $origUser           = $userRepository->findOneBy(array('id' => $_SESSION['user_id']));
        $eventObject->setAuthor($origUser);

        $locationRepository = $entityManager->getRepository(EntityNames::LOCATION);
        $origLocation       = $locationRepository->findOneBy(array('id' => $eventObject->getLocation()->getId()));
        $eventObject->setLocation($origLocation);

        if (null !== $eventObject->getRepeatOption()) {
            $repeatOptionRepository = $entityManager->getRepository(EntityNames::REPEAT_OPTION);
            $origRepeatOption   = $repeatOptionRepository->findOneBy(array('id' => $eventObject->getRepeatOption()->getId()));
            $eventObject->setRepeatOption($origRepeatOption);
        }

        $fileRepository     = $entityManager->getRepository(EntityNames::FILE);
        $origFile           = $fileRepository->findOneBy(array('id' => $eventObject->getFile()));
        $eventObject->setFile($origFile);

        $entityManager->persist($eventObject);

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
        $this->app->response->setBody($this->app->serializer->serialize($eventObject, 'json'));
    }

    public function deleteEventByIdAction($id) {
        $entityManager      = $this->app->entityManager;
        $eventRepository    = $entityManager->getRepository(EntityNames::EVENT);
        $event              = $eventRepository->findOneBy(array('id' => $id));

        if ($event === null) {
            $this->app->response->setStatus(HttpStatusCodes::NOT_FOUND);
            return;
        }

        // prevent conflict on foreign key constraint
        $event->setIsLockedBy(null);

        $entityManager->remove($event);

        try {
            $entityManager->flush();
        } catch (DBALException $dbalex) {
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $dbalex->getMessage()));
            $this->app->response->setStatus(HttpStatusCodes::CONFLICT);
        }
        
        $this->app->response->setStatus(HttpStatusCodes::NO_CONTENT);
    }

    public function getEmptyEventAction() {
        $event  = new Event();

        $userRepository = $this->app->entityManager->getRepository(EntityNames::USER);
        $origUser       = $userRepository->findOneBy(array('id' => $_SESSION['user_id']));
        $event->setAuthor($origUser);

        $now = new DateTime();
        $event->setLastEditDate($now);
        $event->setCreationDate($now);

        $this->app->response->header('Content-Type', 'application/json');
        $this->app->response->setStatus(HttpStatusCodes::OK);
        $this->app->response->setBody($this->app->serializer->serialize($event, 'json'));
    }
}