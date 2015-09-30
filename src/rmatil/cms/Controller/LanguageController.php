<?php

namespace rmatil\cms\Controller;

use DateTime;
use Doctrine\DBAL\DBALException;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Constants\HttpStatusCodes;
use rmatil\cms\Entities\Language;
use rmatil\cms\Exceptions\EntityInvalidException;
use rmatil\cms\Exceptions\EntityNotFoundException;
use rmatil\cms\Exceptions\EntityNotInsertedException;
use rmatil\cms\Exceptions\EntityNotUpdatedException;
use rmatil\cms\Response\ResponseFactory;
use SlimController\SlimController;
use Symfony\Component\Validator\Tests\Fixtures\Entity;

/**
 * @package rmatil\cms\Controller
 */
class LanguageController extends SlimController {

    public function getLanguagesAction() {
        ResponseFactory::createJsonResponse(
            $this->app,
            $this->app
                ->dataAccessorFactory
                ->getDataAccessor(EntityNames::LANGUAGE)
                ->getAll()
        );
    }

    public function getLanguageByIdAction($id) {
        try {
            ResponseFactory::createJsonResponse(
                $this->app,
                $this->app
                    ->dataAccessorFactory
                    ->getDataAccessor(EntityNames::LANGUAGE)
                    ->getById($id)
            );
        } catch (EntityNotFoundException $enfe) {
            ResponseFactory::createNotFoundResponse($this->app, $enfe->getMessage());
        }
    }

    public function updateLanguageAction($languageId) {
        /** @var \rmatil\cms\Entities\Language $language */
        $language = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::LANGUAGE, 'json');
        $language->setId($languageId);

        try {
            $language = $this->app
                ->dataAccessorFactory
                ->getDataAccessor(EntityNames::LANGUAGE)
                ->update($language);
        }  catch (EntityInvalidException $eie) {
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::BAD_REQUEST, $eie->getMessage());
            return;
        } catch (EntityNotFoundException $enfe) {
            ResponseFactory::createNotFoundResponse($this->app, $enfe->getMessage());
            return;
        } catch (EntityNotUpdatedException $enue) {
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $enue->getMessage());
            return;
        }

        ResponseFactory::createJsonResponse($this->app, $language);
    }

    public function insertLanguageAction() {
        /** @var \rmatil\cms\Entities\Language $language */
        $language = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::LANGUAGE, 'json');

        try {
            $language = $this->app
                ->dataAccessorFactory
                ->getDataAccessor(EntityNames::LANGUAGE)
                ->insert($language);
        } catch (EntityNotInsertedException $enie) {
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $enie->getMessage());
        }

        ResponseFactory::createJsonResponseWithCode($this->app, HttpStatusCodes::CREATED, $language);
    }

    public function deleteLanguageByIdAction($id) {
        try {
            $this->app
                ->dataAccessorFactory
                ->getDataAccessor(EntityNames::LANGUAGE)
                ->delete($id);
        } catch (EntityNotFoundException $enfe) {
            ResponseFactory::createNotFoundResponse($this->app, 'Could not find language');
            return;
        }

        $this->app->response->setStatus(HttpStatusCodes::NO_CONTENT);
    }
}