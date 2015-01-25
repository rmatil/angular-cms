<?php

namespace rmatil\cms\Controller;

use SlimController\SlimController;
use rmatil\cms\Constants\HttpStatusCodes;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Entities\Article;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\DBALException;
use DateTime;

class PageCategoryController extends SlimController {

    public function getPageCategoriesAction() {
        $entityManager              = $this->app->entityManager;
        $pageCategoryRepository     = $entityManager->getRepository(EntityNames::PAGE_CATEGORY);
        $pageCategories             = $pageCategoryRepository->findAll();

        $this->app->expires(0);
        $this->app->response->header('Content-Type', 'application/json');
        $this->app->response->setStatus(HttpStatusCodes::OK);
        $this->app->response->setBody($this->app->serializer->serialize($pageCategories, 'json'));
    }

    public function getPageCategoryByIdAction($id) {
        $entityManager              = $this->app->entityManager;
        $pageCategoryRepository     = $entityManager->getRepository(EntityNames::PAGE);
        $pageCategory               = $pageCategoryRepository->findOneBy(array('id' => $id));

        if ($pageCategory === null) {
            $this->app->response->setStatus(HttpStatusCodes::NOT_FOUND);
            return;
        }

        $this->app->expires(0);
        $this->app->response->header('Content-Type', 'application/json');
        $this->app->response->setStatus(HttpStatusCodes::OK);
        $this->app->response->setBody($this->app->serializer->serialize($pageCategory, 'json'));
    }

    public function updatePageCategoryAction($pageCategoryId) {
        $pageCategoryObj            = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::PAGE, 'json');

        // get original page category
        $entityManager              = $this->app->entityManager;
        $pageCategoryRepository     = $entityManager->getRepository(EntityNames::PAGE);
        $origPageCategory           = $pageCategoryRepository->findOneBy(array('id' => $pageCategoryId));

        $origPageCategory->update($pageCategoryObj);

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
        $this->app->response->setBody($this->app->serializer->serialize($origPageCategory, 'json'));
    }

    public function insertPageAction() {
        $pageCategoryObj          = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::PAGE, 'json');

        $entityManager            = $this->app->entityManager;
        $entityManager->persist($pageCategoryObj);

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
        $this->app->response->setBody($this->app->serializer->serialize($pageCategoryObj, 'json'));
    }

    public function deletePageCategoryByIdAction($id) {
        $entityManager              = $this->app->entityManager;
        $pageCategoryRepository     = $entityManager->getRepository(EntityNames::PAGE);
        $pageCategory               = $pageCategoryRepository->findOneBy(array('id' => $id));

        if ($pageCategory === null) {
            $this->app->response->setStatus(HttpStatusCodes::NOT_FOUND);
            return;
        }

        $entityManager->remove($pageCategory);

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