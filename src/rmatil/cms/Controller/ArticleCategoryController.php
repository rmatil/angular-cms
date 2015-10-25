<?php

namespace rmatil\cms\Controller;

use DateTime;
use Doctrine\DBAL\DBALException;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Constants\HttpStatusCodes;
use rmatil\cms\Entities\ArticleCategory;
use rmatil\cms\Exceptions\EntityInvalidException;
use rmatil\cms\Exceptions\EntityNotFoundException;
use rmatil\cms\Exceptions\EntityNotInsertedException;
use rmatil\cms\Exceptions\EntityNotUpdatedException;
use rmatil\cms\Response\ResponseFactory;
use Slim\Http\Response;
use SlimController\SlimController;

/**
 * @package rmatil\cms\Controller
 */
class ArticleCategoryController extends SlimController {

    public function getArticleCategoriesAction() {
        ResponseFactory::createJsonResponse(
            $this->app,
            $this->app
                ->dataAccessorFactory
                ->getDataAccessor(EntityNames::ARTICLE_CATEGORY)
                ->getAll());
    }

    public function getArticleCategoryByIdAction($id) {
        try {
            ResponseFactory::createJsonResponse(
                $this->app,
                $this->app
                    ->dataAccessorFactory
                    ->getDataAccessor(EntityNames::ARTICLE_CATEGORY)
                    ->getById($id));
        } catch (EntityNotFoundException $enfe) {
            ResponseFactory::createNotFoundResponse($this->app, $enfe->getMessage());
        }
    }

    public function updateArticleCategoryAction($articleCategoryId) {
        /** @var \rmatil\cms\Entities\ArticleCategory $articleCategory */
        $articleCategory = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::ARTICLE_CATEGORY, 'json');
        $articleCategory->setId($articleCategoryId);

        try {
            $obj = $this->app
                ->dataAccessorFactory
                ->getDataAccessor(EntityNames::ARTICLE_CATEGORY)
                ->update($articleCategory);
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

    public function insertArticleCategoryAction() {
        $articleCategory = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::ARTICLE_CATEGORY, 'json');

        try {
            $articleCategory = $this->app
                ->dataAccessorFactory
                ->getDataAccessor(EntityNames::ARTICLE_CATEGORY)
                ->insert($articleCategory);
        } catch (EntityNotInsertedException $enie) {
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $enie->getMessage());
            return;
        }

        ResponseFactory::createJsonResponseWithCode($this->app, HttpStatusCodes::CREATED, $articleCategory);
    }

    public function deleteArticleCategoryByIdAction($id) {
        try {
            $this->app
                ->dataAccessorFactory
                ->getDataAccessor(EntityNames::ARTICLE_CATEGORY)
                ->delete($id);
        } catch (EntityNotFoundException $enfe) {
            ResponseFactory::createNotFoundResponse($this->app, 'Could not find article category');
            return;
        }

        $this->app->response->setStatus(HttpStatusCodes::NO_CONTENT);
    }
}