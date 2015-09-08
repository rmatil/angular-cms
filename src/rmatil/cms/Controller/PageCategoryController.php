<?php

namespace rmatil\cms\Controller;

use DateTime;
use Doctrine\DBAL\DBALException;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Constants\HttpStatusCodes;
use rmatil\cms\Entities\PageCategory;
use rmatil\cms\Response\ResponseFactory;
use SlimController\SlimController;

/**
 * @package rmatil\cms\Controller
 */
class PageCategoryController extends SlimController {

    public function getPageCategoriesAction() {
        $entityManager = $this->app->entityManager;
        $pageCategoryRepository = $entityManager->getRepository(EntityNames::PAGE_CATEGORY);
        $pageCategories = $pageCategoryRepository->findAll();

        ResponseFactory::createJsonResponse($this->app, $pageCategories);
    }

    public function getPageCategoryByIdAction($id) {
        $entityManager = $this->app->entityManager;
        $pageCategoryRepository = $entityManager->getRepository(EntityNames::PAGE);
        $pageCategory = $pageCategoryRepository->findOneBy(array('id' => $id));

        if ( ! ($pageCategory instanceof PageCategory)) {
            ResponseFactory::createNotFoundResponse($this->app, 'Could not find page category');
            return;
        }

        ResponseFactory::createJsonResponse($this->app, $pageCategory);
    }

    public function updatePageCategoryAction($pageCategoryId) {
        /** @var \rmatil\cms\Entities\PageCategory $pageCategoryObj */
        $pageCategoryObj = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::PAGE, 'json');

        // get original page category
        $entityManager = $this->app->entityManager;
        $pageCategoryRepository = $entityManager->getRepository(EntityNames::PAGE);
        $origPageCategory = $pageCategoryRepository->findOneBy(array('id' => $pageCategoryId));

        if (!($origPageCategory instanceof PageCategory)) {
            ResponseFactory::createNotFoundResponse($this->app, 'Could not find page category');
            return;
        }

        $origPageCategory->update($pageCategoryObj);

        // force update
        try {
            $entityManager->flush();
        } catch (DBALException $dbalex) {
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $dbalex->getMessage()));
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $dbalex->getMessage());
            return;
        }

        ResponseFactory::createJsonResponse($this->app, $origPageCategory);
    }

    public function insertPageAction() {
        /** @var \rmatil\cms\Entities\PageCategory $pageCategoryObj */
        $pageCategoryObj = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::PAGE, 'json');

        $entityManager = $this->app->entityManager;
        $entityManager->persist($pageCategoryObj);

        try {
            $entityManager->flush();
        } catch (DBALException $dbalex) {
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $dbalex->getMessage()));
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $dbalex->getMessage());
            return;
        }

        ResponseFactory::createJsonResponseWithCode($this->app, HttpStatusCodes::CREATED, $pageCategoryObj);
    }

    public function deletePageCategoryByIdAction($id) {
        $entityManager = $this->app->entityManager;
        $pageCategoryRepository = $entityManager->getRepository(EntityNames::PAGE);
        $pageCategory = $pageCategoryRepository->findOneBy(array('id' => $id));

        if (!($pageCategory instanceof PageCategory)) {
            ResponseFactory::createNotFoundResponse($this->app, 'Could not find page category');
            return;
        }

        $entityManager->remove($pageCategory);

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