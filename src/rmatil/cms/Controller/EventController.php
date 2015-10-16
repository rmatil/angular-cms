<?php

namespace rmatil\cms\Controller;

use DateTime;
use DateTimeZone;
use Doctrine\DBAL\DBALException;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Constants\HttpStatusCodes;
use rmatil\cms\Entities\Event;
use rmatil\cms\Entities\Location;
use rmatil\cms\Entities\RepeatOption;
use rmatil\cms\Entities\User;
use rmatil\cms\Exceptions\EntityInvalidException;
use rmatil\cms\Exceptions\EntityNotFoundException;
use rmatil\cms\Exceptions\EntityNotInsertedException;
use rmatil\cms\Exceptions\EntityNotUpdatedException;
use rmatil\cms\Response\ResponseFactory;
use Slim\Http\Response;
use SlimController\SlimController;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @package rmatil\cms\Controller
 */
class EventController extends SlimController {

    public function getEventsAction() {
        ResponseFactory::createJsonResponse(
            $this->app,
            $this->app
                ->dataAccessorFactory
                ->getDataAccessor(EntityNames::EVENT)
                ->getAll()
        );
    }

    public function getEventByIdAction($id) {
        try {
            ResponseFactory::createJsonResponse(
                $this->app,
                $this->app
                    ->dataAccessorFactory
                    ->getDataAccessor(EntityNames::EVENT)
                    ->getById($id)
            );
        } catch (EntityNotFoundException $enfe) {
            ResponseFactory::createNotFoundResponse(
                $this->app,
                $enfe->getMessage()
            );
        }
    }

    public function updateEventAction($id) {
        $now = new DateTime('now', new DateTimeZone("UTC"));
        /** @var \rmatil\cms\Entities\Event $eventObject */
        $eventObject = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::EVENT, 'json');
        $eventObject->setId($id);
        $eventObject->setLastEditDate($now);
        $eventObject->setAuthor(
            $this->app
                ->entityManager
                ->getRepository(EntityNames::USER)
                ->find($_SESSION['user_id'])
        );

        try {
            $obj = $this->app
                    ->dataAccessorFactory
                    ->getDataAccessor(EntityNames::EVENT)
                    ->update($eventObject);
        } catch (EntityInvalidException $eie) {
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::BAD_REQUEST, $eie->getMessage());
            return;
        } catch (EntityNotFoundException $enfe) {
            ResponseFactory::createNotFoundResponse($this->app, $enfe->getMessage());
            return;
        } catch (EntityNotUpdatedException $enue) {
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $enue->getMessage());
            return;
        }

        ResponseFactory::createJsonResponse($this->app, $obj);
    }

    public function insertEventAction() {
        /** @var \rmatil\cms\Entities\Event $eventObject */
        $eventObject = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::EVENT, 'json');
        $eventObject->setAuthor(
            $this->app
                ->entityManager
                ->getRepository(EntityNames::USER)
                ->find($_SESSION['user_id'])
        );

        try {
            $event = $this->app
                    ->dataAccessorFactory
                    ->getDataAccessor(EntityNames::EVENT)
                    ->insert($eventObject);
        } catch (EntityNotInsertedException $enie) {
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $enie->getMessage());
            return;
        }

        ResponseFactory::createJsonResponseWithCode($this->app, HttpStatusCodes::CREATED, $event);
    }

    public function deleteEventByIdAction($id) {
        try {
            $this->app
                ->dataAccessorFactory
                ->getDataAccessor(EntityNames::EVENT)
                ->delete($id);
        } catch (EntityNotFoundException $enfe) {
            ResponseFactory::createNotFoundResponse($this->app, 'Could not find eventt');
            return;
        }

        $this->app->response->setStatus(HttpStatusCodes::NO_CONTENT);
    }

    public function getEmptyEventAction() {
        $event = new Event();

        $event->setAuthor(
            $this->app->entityManager->getRepository(EntityNames::USER)->find($_SESSION['user_id'])
        );

        $now = new DateTime('now', new DateTimeZone('UTC'));
        $event->setLastEditDate($now);
        $event->setCreationDate($now);

        ResponseFactory::createJsonResponse($this->app, $event);
    }
}