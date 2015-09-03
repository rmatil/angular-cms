<?php

namespace rmatil\cms\Controller;

use DateTime;
use Doctrine\DBAL\DBALException;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Constants\HttpStatusCodes;
use rmatil\cms\Entities\ArticleCategory;
use rmatil\cms\Response\ResponseFactory;
use SlimController\SlimController;

/**
 * @package rmatil\cms\Controller
 */
class ArticleCategoryController extends SlimController {

    public function getArticleCategoriesAction() {
        $articleCategoryRepository = $this->app->entityManager->getRepository(EntityNames::ARTICLE_CATEGORY);
        $articleCategories = $articleCategoryRepository->findAll();

        ResponseFactory::createJsonResponse($this->app, $articleCategories);
    }

    public function getArticleCategoryByIdAction($id) {
        $articleCategoryRepository = $this->app->entityManager->getRepository(EntityNames::ARTICLE_CATEGORY);
        $articleCategory = $articleCategoryRepository->findOneBy(array('id' => $id));

        if ( ! ($articleCategory instanceof ArticleCategory)) {
            $this->app->response->setStatus(HttpStatusCodes::NOT_FOUND);
            return;
        }

        ResponseFactory::createJsonResponse($this->app, $articleCategory);
    }

    public function updateArticleCategoryAction($articleCategoryId) {
        $entityManager = $this->app->entityManager;
        /** @var \rmatil\cms\Entities\ArticleCategory $articleCategoryObj */
        $articleCategoryObj = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::ARTICLE_CATEGORY, 'json');

        // get original article
        $articleCategoryRepository = $entityManager->getRepository(EntityNames::ARTICLE_CATEGORY);
        $origArticleCategory = $articleCategoryRepository->findOneBy(array('id' => $articleCategoryId));

        if ( ! ($origArticleCategory instanceof ArticleCategory)) {
            ResponseFactory::createNotFoundResponse($this->app);
            return;
        }

        $origArticleCategory->update($articleCategoryObj);

        // force update
        try {
            $entityManager->flush();
        } catch (DBALException $dbalex) {
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $dbalex->getMessage()));
            $this->app->response->setStatus(HttpStatusCodes::CONFLICT);
            return;
        }

        ResponseFactory::createJsonResponse($this->app, $origArticleCategory);
    }

    public function insertArticleAction() {
        $articleCategoryObj = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::ARTICLE_CATEGORY, 'json');

        $entityManager = $this->app->entityManager;
        $entityManager->persist($articleCategoryObj);

        try {
            $entityManager->flush();
        } catch (DBALException $dbalex) {
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $dbalex->getMessage()));
            $this->app->response->setStatus(HttpStatusCodes::CONFLICT);
            return;
        }

        ResponseFactory::createJsonResponseWithCode($this->app, HttpStatusCodes::CREATED, $articleCategoryObj);
    }

    public function deleteArticleCategoryByIdAction($id) {
        $entityManager = $this->app->entityManager;
        $articleCategoryRepository = $entityManager->getRepository(EntityNames::ARTICLE_CATEGORY);
        $articleCategory = $articleCategoryRepository->findOneBy(array('id' => $id));

        if ( ! ($articleCategory instanceof ArticleCategory)) {
            ResponseFactory::createNotFoundResponse($this->app);
            return;
        }

        $entityManager->remove($articleCategory);

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