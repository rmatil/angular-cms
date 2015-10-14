<?php

namespace rmatil\cms\Controller;

use DateTime;
use Doctrine\DBAL\DBALException;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Constants\HttpStatusCodes;
use rmatil\cms\Entities\PageCategory;
use rmatil\cms\Exceptions\EntityInvalidException;
use rmatil\cms\Exceptions\EntityNotFoundException;
use rmatil\cms\Exceptions\EntityNotInsertedException;
use rmatil\cms\Exceptions\EntityNotUpdatedException;
use rmatil\cms\Response\ResponseFactory;
use SlimController\SlimController;

/**
 * @package rmatil\cms\Controller
 */
class PageCategoryController extends SlimController {

    public function getPageCategoriesAction() {
        ResponseFactory::createJsonResponse(
            $this->app,
            $this->app
                ->dataAccessorFactory
                ->getDataAccessor(EntityNames::PAGE_CATEGORY)
                ->getAll()
        );
    }

    public function getPageCategoryByIdAction($id) {
        try {
            ResponseFactory::createJsonResponse(
                $this->app,
                $this->app
                    ->dataAccessorFactory
                    ->getDataAccessor(EntityNames::PAGE_CATEGORY)
                    ->getById($id)
            );
        } catch (EntityNotFoundException $enfe) {
            ResponseFactory::createNotFoundResponse($this->app, $enfe->getMessage());
        }
    }

    public function updatePageCategoryAction($pageCategoryId) {
        /** @var \rmatil\cms\Entities\PageCategory $pageCategory */
        $pageCategory = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::PAGE_CATEGORY, 'json');
        $pageCategory->setId($pageCategoryId);

        try {
            $pageCategory = $this->app
                ->dataAccessorFactory
                ->getDataAccessor(EntityNames::PAGE_CATEGORY)
                ->update($pageCategory);
        } catch (EntityInvalidException $eie) {
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $eie->getMessage());
            return;
        } catch (EntityNotFoundException $enfe) {
            ResponseFactory::createNotFoundResponse($this->app, $enfe->getMessage());
            return;
        } catch (EntityNotUpdatedException $enue) {
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $enue->getMessage());
            return;
        }

        ResponseFactory::createJsonResponse($this->app, $pageCategory);
    }

    public function insertPageCategoryAction() {
        /** @var \rmatil\cms\Entities\PageCategory $pageCategory */
        $pageCategory = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::PAGE_CATEGORY, 'json');

        try {
            $pageCategory = $this->app
                ->dataAccessorFactory
                ->getDataAccessor(EntityNames::PAGE_CATEGORY)
                ->insert($pageCategory);
        } catch (EntityNotInsertedException $enie) {
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $enie->getMessage());
            return;
        }

        ResponseFactory::createJsonResponseWithCode($this->app, HttpStatusCodes::CREATED, $pageCategory);
    }

    public function deletePageCategoryByIdAction($id) {
        try {
            $this->app
                ->dataAccessorFactory
                ->getDataAccessor(EntityNames::PAGE_CATEGORY)
                ->delete($id);
        } catch (EntityNotFoundException $enfe) {
            ResponseFactory::createNotFoundResponse($this->app, $enfe->getMessage());
            return;
        }

        $this->app->response->setStatus(HttpStatusCodes::NO_CONTENT);
    }
}