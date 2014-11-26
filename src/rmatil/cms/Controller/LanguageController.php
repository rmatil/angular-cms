<?php

namespace rmatil\cms\Controller;

use SlimController\SlimController;
use rmatil\cms\Constants\HttpStatusCodes;
use rmatil\cms\Entities\Article;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\DBALException;
use DateTime;

class LanguageController extends SlimController {

    private static $LANGUAGE_FULL_QUALIFIED_CLASSNAME = 'rmatil\cms\Entities\Language';

    public function getLanguagesAction() {
        $entityManager      = $this->app->entityManager;
        $languageRepository = $entityManager->getRepository(self::$LANGUAGE_FULL_QUALIFIED_CLASSNAME);
        $languages          = $languageRepository->findAll();

        $this->app->response->header('Content-Type', 'application/json');
        $this->app->response->setStatus(HttpStatusCodes::OK);
        $this->app->response->setBody($this->app->serializer->serialize($languages, 'json'));
    }

    public function getLanguageByIdAction($id) {
        $entityManager      = $this->app->entityManager;
        $languageRepository = $entityManager->getRepository(self::$LANGUAGE_FULL_QUALIFIED_CLASSNAME);
        $language           = $languageRepository->findOneBy(array('id' => $id));

        if ($language === null) {
            $this->app->response->setStatus(HttpStatusCodes::NOT_FOUND);
            return;
        }

        $this->app->response->header('Content-Type', 'application/json');
        $this->app->response->setStatus(HttpStatusCodes::OK);
        $this->app->response->setBody($this->app->serializer->serialize($language, 'json'));
    }

    public function updateLanguageAction($languageId) {
        $languageObject      = $this->app->serializer->deserialize($this->app->request->getBody(), self::$LANGUAGE_FULL_QUALIFIED_CLASSNAME, 'json');

        // get original article
        $entityManager      = $this->app->entityManager;
        $languageRepository = $entityManager->getRepository(self::$LANGUAGE_FULL_QUALIFIED_CLASSNAME);
        $origLanguage       = $languageRepository->findOneBy(array('id' => $languageId));

        $origLanguage->update($languageObject);

        // force update
        try {
            $entityManager->flush();
        } catch (DBALException $dbalex) {
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $dbalex->getMessage()));
            $this->app->response->setStatus(HttpStatusCodes::CONFLICT);
            return;
        }

        $this->app->response->header('Content-Type', 'application/json');
        $this->app->response->setStatus(HttpStatusCodes::OK);
        $this->app->response->setBody($this->app->serializer->serialize($origLanguage, 'json'));
    }

    public function insertLanguageAction() {
        $languageObject      = $this->app->serializer->deserialize($this->app->request->getBody(), self::$LANGUAGE_FULL_QUALIFIED_CLASSNAME, 'json');

        $entityManager      = $this->app->entityManager;
        $entityManager->persist($languageObject);

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
        $this->app->response->setBody($this->app->serializer->serialize($languageObject, 'json'));
    }

    public function deleteLanguageByIdAction($id) {
        $entityManager       = $this->app->entityManager;
        $languageRepository  = $entityManager->getRepository(self::$LANGUAGE_FULL_QUALIFIED_CLASSNAME);
        $language            = $languageRepository->findOneBy(array('id' => $id));

        if ($language === null) {
            $this->app->response->setStatus(HttpStatusCodes::NOT_FOUND);
            return;
        }

        $entityManager->remove($language);

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