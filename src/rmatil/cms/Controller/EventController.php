<?php

namespace rmatil\cms\Controller;

use DateTime;
use Doctrine\DBAL\DBALException;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Constants\HttpStatusCodes;
use rmatil\cms\Entities\Event;
use rmatil\cms\Entities\Location;
use rmatil\cms\Entities\RepeatOption;
use rmatil\cms\Entities\User;
use rmatil\cms\Response\ResponseFactory;
use SlimController\SlimController;

/**
 * @package rmatil\cms\Controller
 */
class EventController extends SlimController {

    public function getEventsAction() {
        $entityManager = $this->app->entityManager;
        $eventRepository = $entityManager->getRepository(EntityNames::EVENT);
        $events = $eventRepository->findAll();

        ResponseFactory::createJsonResponse($this->app, $events);
    }

    public function getEventByIdAction($id) {
        $entityManager = $this->app->entityManager;
        $eventRepository = $entityManager->getRepository(EntityNames::EVENT);
        $event = $eventRepository->findOneBy(array('id' => $id));

        if ( ! ($event instanceof Event)) {
            $this->app->response->setStatus(HttpStatusCodes::NOT_FOUND);
            return;
        }

        // do not show lock if requested by the same user as currently locked
        if (($event->getIsLockedBy() instanceof User) &&
            $event->getIsLockedBy()->getId() === $_SESSION['user_id']
        ) {
            $event->setIsLockedBy(null);
        }

        $userRepository = $this->app->entityManager->getRepository(EntityNames::USER);
        $origUser = $userRepository->findOneBy(array('id' => $_SESSION['user_id']));
        $event->setAuthor($origUser);

        ResponseFactory::createJsonResponse($this->app, $event);

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
        /** @var \rmatil\cms\Entities\Event $eventObject */
        $eventObject = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::EVENT, 'json');

        // get original event
        $entityManager = $this->app->entityManager;
        $eventRepository = $entityManager->getRepository(EntityNames::EVENT);
        $origEvent = $eventRepository->findOneBy(array('id' => $eventId));

        if ( ! ($origEvent instanceof Event)) {
            $this->app->response->setStatus(HttpStatusCodes::NOT_FOUND);
            return;
        }

        // update author
        $userRepository = $entityManager->getRepository(EntityNames::USER);
        $origUser = $userRepository->findOneBy(array('id' => $_SESSION['user_id']));
        $eventObject->setAuthor($origUser);

        if ($eventObject->getLocation() instanceof Location) {
            $locationRepository = $entityManager->getRepository(EntityNames::LOCATION);
            $origLocation = $locationRepository->findOneBy(array('id' => $eventObject->getLocation()->getId()));
            $eventObject->setLocation($origLocation);
        }

        if ($eventObject->getRepeatOption() instanceof RepeatOption) {
            $repeatOptionRepository = $entityManager->getRepository(EntityNames::REPEAT_OPTION);
            $origRepeatOption = $repeatOptionRepository->findOneBy(array('id' => $eventObject->getRepeatOption()->getId()));
            $eventObject->setRepeatOption($origRepeatOption);
        }

        $fileRepository = $entityManager->getRepository(EntityNames::FILE);
        $origFile = $fileRepository->findOneBy(array('id' => $eventObject->getFile()));
        $eventObject->setFile($origFile);

        // get all allowed usergroups
        $userGroupRepo = $entityManager->getRepository(EntityNames::USER_GROUP);
        $allUserGroups = $userGroupRepo->findAll();

        foreach ($allUserGroups as $userGroup) {
            if ($userGroup->getAccessibleEvents()->contains($origEvent) &&
                ! $origEvent->getAllowedUserGroups()->contains($userGroup)
            ) {
                // maintain inverse side
                $origEvent->addAllowedUserGroup($userGroup);
            } else if ( ! $userGroup->getAccessibleEvents()->contains($origEvent) &&
                $origEvent->getAllowedUserGroups()->contains($userGroup)
            ) {
                // maintain inverse side
                $origEvent->removeAllowedUserGroup($userGroup);
            }

            if ( ! $userGroup->getAccessibleEvents()->contains($origEvent) &&
                ! $origEvent->getAllowedUserGroups()->contains($userGroup)
            ) {
                // use this loop here, as contains() does not
                // consider a proxy object as a equally object. Basically, it isn't...
                foreach ($eventObject->getAllowedUserGroups() as $userGroupObj) {
                    if ($userGroupObj->getId() === $userGroup->getId()) {
                        // usergroup was selected and we can add the event to the accessible usergroups
                        // and the usergroup as allowedUserGroup to the event (inside addAccessibleEvent-Method)
                        $userGroup->addAccessibleEvent($origEvent);
                        break;
                    }
                }

            } else if ($userGroup->getAccessibleEvents()->contains($origEvent) &&
                $origEvent->getAllowedUserGroups()->contains($userGroup) &&
                ! $eventObject->getAllowedUserGroups()->contains($userGroup)
            ) {
                $doesContainObj = false;
                foreach ($eventObject->getAllowedUserGroups() as $userGroupObj) {
                    if ($userGroupObj->getId() === $userGroup->getId()) {
                        $doesContainObj = true;
                        break;
                    }
                }

                if ( ! $doesContainObj) {
                    // usegroup was unselected and we can remove the event from the accessible usergroups
                    // and the usergroup as the allowedUserGroup from the event (inside removeAccessibleEvent)
                    $userGroup->removeAccessibleEvent($origEvent);
                }
            }
        }

        $origEvent->setAuthor($eventObject->getAuthor());
        $origEvent->setLocation($eventObject->getLocation());
        $origEvent->setFile($eventObject->getFile());
        $origEvent->setName($eventObject->getName());
        $origEvent->setRepeatOption($eventObject->getRepeatOption());
        $origEvent->setStartDate($eventObject->getStartDate());
        $origEvent->setEndDate($eventObject->getEndDate());
        $origEvent->setDescription($eventObject->getDescription());
        $origEvent->setLastEditDate($eventObject->getLastEditDate());
        $origEvent->setCreationDate($eventObject->getCreationDate());

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

        ResponseFactory::createJsonResponse($this->app, $origEvent);
    }

    public function insertEventAction() {
        /** @var \rmatil\cms\Entities\Event $eventObject */
        $eventObject = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::EVENT, 'json');

        // set now as creation date
        $now = new DateTime();
        $eventObject->setLastEditDate($now);
        $eventObject->setCreationDate($now);

        $entityManager = $this->app->entityManager;

        $userRepository = $entityManager->getRepository(EntityNames::USER);
        $origUser = $userRepository->findOneBy(array('id' => $_SESSION['user_id']));
        $eventObject->setAuthor($origUser);

        if ($eventObject->getLocation() instanceof Location) {
            $locationRepository = $entityManager->getRepository(EntityNames::LOCATION);
            $origLocation = $locationRepository->findOneBy(array('id' => $eventObject->getLocation()->getId()));
            $eventObject->setLocation($origLocation);
        }

        if ($eventObject->getRepeatOption() instanceof RepeatOption) {
            $repeatOptionRepository = $entityManager->getRepository(EntityNames::REPEAT_OPTION);
            $origRepeatOption = $repeatOptionRepository->findOneBy(array('id' => $eventObject->getRepeatOption()->getId()));
            $eventObject->setRepeatOption($origRepeatOption);
        }

        $fileRepository = $entityManager->getRepository(EntityNames::FILE);
        $origFile = $fileRepository->findOneBy(array('id' => $eventObject->getFile()));
        $eventObject->setFile($origFile);

        // get all allowed usergroups
        $userGroupObjs = $eventObject->getAllowedUserGroups()->toArray(); // use array here, otherwise this reference will also be empty after clear()
        $eventObject->getAllowedUserGroups()->clear();
        $userGroupRepo = $entityManager->getRepository(EntityNames::USER_GROUP);
        $allUserGroups = $userGroupRepo->findAll();

        foreach ($allUserGroups as $userGroup) {
            foreach ($userGroupObjs as $userGroupObj) {
                if ($userGroupObj->getId() === $userGroup->getId()) {
                    // usergroup was selected and we can add the article to the accessible usergroups
                    // and the usergroup as allowedUserGroup to the article (inside addAccessibleArticle-Method)
                    $userGroup->addAccessibleEvent($eventObject);
                    break;
                }
            }
        }

        $entityManager->persist($eventObject);

        try {
            $entityManager->flush();
        } catch (DBALException $dbalex) {
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $dbalex->getMessage()));
            $this->app->response->setStatus(HttpStatusCodes::CONFLICT);
            return;
        }

        ResponseFactory::createJsonResponseWithCode($this->app, HttpStatusCodes::CREATED, $eventObject);
    }

    public function deleteEventByIdAction($id) {
        $entityManager = $this->app->entityManager;
        $eventRepository = $entityManager->getRepository(EntityNames::EVENT);
        $event = $eventRepository->findOneBy(array('id' => $id));

        if ( ! ($event instanceof Event)) {
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
        $event = new Event();

        $userRepository = $this->app->entityManager->getRepository(EntityNames::USER);
        $origUser = $userRepository->findOneBy(array('id' => $_SESSION['user_id']));
        $event->setAuthor($origUser);

        $now = new DateTime();
        $event->setLastEditDate($now);
        $event->setCreationDate($now);

        ResponseFactory::createJsonResponse($this->app, $event);
    }
}