<?php

namespace rmatil\cms\Controller;

use DateTime;
use Doctrine\DBAL\DBALException;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Constants\HttpStatusCodes;
use SlimController\SlimController;

class RepeatOptionController extends SlimController {

    public function getRepeatOptionsAction() {
        $entityManager              = $this->app->entityManager;
        $repeatOptionRepository     = $entityManager->getRepository(EntityNames::REPEAT_OPTION);
        $repeatOptions              = $repeatOptionRepository->findAll();

        $this->app->expires(0);
        $this->app->response->header('Content-Type', 'application/json');
        $this->app->response->setStatus(HttpStatusCodes::OK);
        $this->app->response->setBody($this->app->serializer->serialize($repeatOptions, 'json'));
    }

    public function getRepeatOptionByIdAction($id) {
        $entityManager              = $this->app->entityManager;
        $repeatOptionRepository     = $entityManager->getRepository(EntityNames::REPEAT_OPTION);
        $repeatOption               = $repeatOptionRepository->findOneBy(array('id' => $id));

        if ($repeatOption === null) {
            $this->app->response->setStatus(HttpStatusCodes::NOT_FOUND);
        }

        $this->app->expires(0);
        $this->app->response->header('Content-Type', 'application/json');
        $this->app->response->setStatus(HttpStatusCodes::OK);
        $this->app->response->setBody($this->app->serializer->serialize($repeatOption, 'json'));
    }

    public function updateRepeatOptionAction($repeatOptionId) {
        $repeatOptionObj            = $this->app->serializer->serialize($this->app->request->getBody(), EntityNames::REPEAT_OPTION, 'json');

        // get original repeat option
        $entityManager              = $this->app->entityManager;
        $repeatOptionRepository     = $entityManager->getRepository(EntityNames::REPEAT_OPTION);
        $origRepeatOption           = $repeatOptionRepository->findOneBy(array('id' => $repeatOptionId));

        $origRepeatOption->update($repeatOptionObj);

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
        $this->app->response->setBody($this->app->serializer->serialize($origRepeatOption, 'json'));
    }

    public function insertRepeatOptionAction() {
        $repeatOptionObj            = $this->app->serializer->serialize($this->app->request->getBody(), EntityNames::REPEAT_OPTION, 'json');

        $entityManager              = $this->app->entityManager;
        $entityManager->persist($repeatOptionObj);

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
        $this->app->response->setBody($this->app->serializer->serialize($repeatOptionObj, 'json'));        
    }

    public function deleteRepeatOptionByIdAction($id) {
        $entityManager              = $this->app->entityManager;
        $repeatOptionRepository     = $entityManager->getRepository(EntityNames::REPEAT_OPTION);
        $repeatOption               = $repeatOptionRepository->findOneBy(array('id' => $id));

        if ($repeatOption === null) {
            $this->app->response->setStatus(HttpStatusCodes::NOT_FOUND);
            return;
        }

        $entityManager->remove($repeatOption);

        try {
            $entityManager->flush();
        } catch (DBALException $dbalex) {
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $dbalex->getMessage()));
            $this->app->response->setStatus(HttpStatusCodes::CONFLICT);
        }

        $this->app->response->setStatus(HttpStatusCodes::NO_CONTENT);
    }
}