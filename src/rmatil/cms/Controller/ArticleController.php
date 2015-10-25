<?php

namespace rmatil\cms\Controller;

use DateTime;
use DateTimeZone;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Constants\HttpStatusCodes;
use rmatil\cms\Entities\Article;
use rmatil\cms\Exceptions\EntityInvalidException;
use rmatil\cms\Exceptions\EntityNotDeletedException;
use rmatil\cms\Exceptions\EntityNotFoundException;
use rmatil\cms\Exceptions\EntityNotInsertedException;
use rmatil\cms\Exceptions\EntityNotUpdatedException;
use rmatil\cms\Response\ResponseFactory;
use SlimController\SlimController;

/**
 * @package rmatil\cms\Controller
 */
class ArticleController extends SlimController {

    public function getArticlesAction() {
        ResponseFactory::createJsonResponse(
            $this->app,
            $this->app
                ->dataAccessorFactory
                ->getDataAccessor(EntityNames::ARTICLE)
                ->getAll()
        );
    }

    public function getArticleByIdAction($id) {
        try {
            ResponseFactory::createJsonResponse(
                $this->app,
                $this->app
                    ->dataAccessorFactory
                    ->getDataAccessor(EntityNames::ARTICLE)
                    ->getById($id)
            );
        } catch (EntityNotFoundException $enfe) {
            ResponseFactory::createNotFoundResponse(
                $this->app,
                $enfe->getMessage()
            );
        }
    }

    public function updateArticleAction($articleId) {
        $now = new DateTime('now', new DateTimeZone("UTC"));
        /** @var \rmatil\cms\Entities\Article $articleObject */
        $articleObject = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::ARTICLE, 'json');
        $articleObject->setId($articleId);
        $articleObject->setLastEditDate($now);
        $articleObject->setAuthor(
            $this->app
                ->entityManager
                ->getRepository(EntityNames::USER)
                ->find($_SESSION['user_id'])
        );

        try {
            $obj = $this->app
                ->dataAccessorFactory
                ->getDataAccessor(EntityNames::ARTICLE)
                ->update($articleObject);
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

    public function insertArticleAction() {
        /** @var \rmatil\cms\Entities\Article $articleObject */
        $articleObject = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::ARTICLE, 'json');
        $articleObject->setAuthor(
            $this->app
                ->entityManager
                ->getRepository(EntityNames::USER)
                ->find($_SESSION['user_id'])
        );

        try {
            $article = $this->app
                ->dataAccessorFactory
                ->getDataAccessor(EntityNames::ARTICLE)
                ->insert($articleObject);
        } catch (EntityNotInsertedException $enie) {
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $enie->getMessage());
            return;
        }

        ResponseFactory::createJsonResponseWithCode($this->app, HttpStatusCodes::CREATED, $article);
    }

    public function deleteArticleByIdAction($id) {
        try {
            $this->app
                ->dataAccessorFactory
                ->getDataAccessor(EntityNames::ARTICLE)
                ->delete($id);
        } catch (EntityNotFoundException $enfe) {
            ResponseFactory::createNotFoundResponse($this->app, 'Could not find article');
            return;
        } catch (EntityNotDeletedException $ende) {
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $ende->getMessage());
            return;
        }


        $this->app->response->setStatus(HttpStatusCodes::NO_CONTENT);
    }

    public function getEmptyArticleAction() {
        $article = new Article();

        $article->setAuthor(
            $this->app->entityManager->getRepository(EntityNames::USER)->find($_SESSION['user_id'])
        );

        $now = new DateTime('now', new DateTimeZone('UTC'));
        $article->setCreationDate($now);
        $article->setLastEditDate($now);

        ResponseFactory::createJsonResponse($this->app, $article);
    }
}