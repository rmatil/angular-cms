<?php

namespace rmatil\cms\Controller;

use DateTime;
use Doctrine\DBAL\DBALException;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Constants\HttpStatusCodes;
use rmatil\cms\Entities\Language;
use rmatil\cms\Response\ResponseFactory;
use SlimController\SlimController;

/**
 * @package rmatil\cms\Controller
 */
class LanguageController extends SlimController {

    public function getLanguagesAction() {
        $entityManager = $this->app->entityManager;
        $languageRepository = $entityManager->getRepository(EntityNames::LANGUAGE);
        $languages = $languageRepository->findAll();

        ResponseFactory::createJsonResponse($this->app, $languages);
    }

    public function getLanguageByIdAction($id) {
        $entityManager = $this->app->entityManager;
        $languageRepository = $entityManager->getRepository(EntityNames::LANGUAGE);
        $language = $languageRepository->findOneBy(array('id' => $id));

        if ( ! ($language instanceof Language)) {
            ResponseFactory::createNotFoundResponse($this->app, 'Could not find language');
            return;
        }

        ResponseFactory::createJsonResponse($this->app, $language);
    }

    public function updateLanguageAction($languageId) {
        $languageObject = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::LANGUAGE, 'json');

        // get original article
        $entityManager = $this->app->entityManager;
        $languageRepository = $entityManager->getRepository(EntityNames::LANGUAGE);
        $origLanguage = $languageRepository->findOneBy(array('id' => $languageId));

        if ( ! ($origLanguage instanceof Language)) {
            ResponseFactory::createNotFoundResponse($this->app, 'Could not find language');
            return;
        }

        $origLanguage->update($languageObject);

        // force update
        try {
            $entityManager->flush();
        } catch (DBALException $dbalex) {
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $dbalex->getMessage()));
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $dbalex->getMessage());
            return;
        }

        ResponseFactory::createJsonResponse($this->app, $origLanguage);
    }

    public function insertLanguageAction() {
        $languageObject = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::LANGUAGE, 'json');

        $entityManager = $this->app->entityManager;
        $entityManager->persist($languageObject);

        try {
            $entityManager->flush();
        } catch (DBALException $dbalex) {
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $dbalex->getMessage()));
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $dbalex->getMessage());
            return;
        }

        ResponseFactory::createJsonResponseWithCode($this->app, HttpStatusCodes::CREATED, $languageObject);
    }

    public function deleteLanguageByIdAction($id) {
        $entityManager = $this->app->entityManager;
        $languageRepository = $entityManager->getRepository(EntityNames::LANGUAGE);
        $language = $languageRepository->findOneBy(array('id' => $id));

        if ( ! ($language instanceof Language)) {
            ResponseFactory::createNotFoundResponse($this->app, 'Could not find language');
            return;
        }

        $entityManager->remove($language);

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