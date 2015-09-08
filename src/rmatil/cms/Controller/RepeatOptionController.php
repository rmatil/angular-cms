<?php

namespace rmatil\cms\Controller;

use DateTime;
use Doctrine\DBAL\DBALException;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Constants\HttpStatusCodes;
use rmatil\cms\Entities\RepeatOption;
use rmatil\cms\Response\ResponseFactory;
use SlimController\SlimController;

/**
 * @package rmatil\cms\Controller
 */
class RepeatOptionController extends SlimController {

    public function getRepeatOptionsAction() {
        $entityManager = $this->app->entityManager;
        $repeatOptionRepository = $entityManager->getRepository(EntityNames::REPEAT_OPTION);
        $repeatOptions = $repeatOptionRepository->findAll();

        ResponseFactory::createJsonResponse($this->app, $repeatOptions);
    }

    public function getRepeatOptionByIdAction($id) {
        $entityManager = $this->app->entityManager;
        $repeatOptionRepository = $entityManager->getRepository(EntityNames::REPEAT_OPTION);
        $repeatOption = $repeatOptionRepository->findOneBy(array('id' => $id));

        if ( ! ($repeatOption instanceof RepeatOption)) {
            ResponseFactory::createNotFoundResponse($this->app, 'Could not find repeat option');
        }

        ResponseFactory::createJsonResponse($this->app, $repeatOption);
    }

    public function updateRepeatOptionAction($repeatOptionId) {
        /** @var \rmatil\cms\Entities\RepeatOption $repeatOptionObj */
        $repeatOptionObj = $this->app->serializer->serialize($this->app->request->getBody(), EntityNames::REPEAT_OPTION, 'json');

        // get original repeat option
        $entityManager = $this->app->entityManager;
        $repeatOptionRepository = $entityManager->getRepository(EntityNames::REPEAT_OPTION);
        $origRepeatOption = $repeatOptionRepository->findOneBy(array('id' => $repeatOptionId));

        if ( ! ($origRepeatOption instanceof RepeatOption)) {
            ResponseFactory::createNotFoundResponse($this->app, 'Could not find repeat option');
            return;
        }

        $origRepeatOption->update($repeatOptionObj);

        // force update
        try {
            $entityManager->flush();
        } catch (DBALException $dbalex) {
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $dbalex->getMessage()));
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $dbalex->getMessage());
            return;
        }

        ResponseFactory::createJsonResponse($this->app, $origRepeatOption);
    }

    public function insertRepeatOptionAction() {
        /** @var \rmatil\cms\Entities\RepeatOption $repeatOptionObj */
        $repeatOptionObj = $this->app->serializer->serialize($this->app->request->getBody(), EntityNames::REPEAT_OPTION, 'json');

        $entityManager = $this->app->entityManager;
        $entityManager->persist($repeatOptionObj);

        try {
            $entityManager->flush();
        } catch (DBALException $dbalex) {
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $dbalex->getMessage()));
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $dbalex->getMessage());
            return;
        }

        ResponseFactory::createJsonResponseWithCode($this->app, HttpStatusCodes::CREATED, $repeatOptionObj);
    }

    public function deleteRepeatOptionByIdAction($id) {
        $entityManager = $this->app->entityManager;
        $repeatOptionRepository = $entityManager->getRepository(EntityNames::REPEAT_OPTION);
        $repeatOption = $repeatOptionRepository->findOneBy(array('id' => $id));

        if ($repeatOption === null) {
            ResponseFactory::createNotFoundResponse($this->app, 'Could not find repeat option');
            return;
        }

        $entityManager->remove($repeatOption);

        try {
            $entityManager->flush();
        } catch (DBALException $dbalex) {
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $dbalex->getMessage()));
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $dbalex->getMessage());
        }

        $this->app->response->setStatus(HttpStatusCodes::NO_CONTENT);
    }
}