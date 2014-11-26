<?php

namespace rmatil\cms\Controller;

use SlimController\SlimController;
use rmatil\cms\Constants\HttpStatusCodes;
use rmatil\cms\Entities\Article;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\DBALException;
use DateTime;

class ArticleCategoryController extends SlimController {

    private static $ARTICLE_FULL_QUALIFIED_CLASSNAME = 'rmatil\cms\Entities\ArticleCategory';

    public function getArticleCategoriesAction() {
        $entityManager              = $this->app->entityManager;
        $articleCategoryRepository  = $entityManager->getRepository(self::$ARTICLE_FULL_QUALIFIED_CLASSNAME);
        $articleCategories          = $articleCategoryRepository->findAll();

        $this->app->response->header('Content-Type', 'application/json');
        $this->app->response->setStatus(HttpStatusCodes::OK);
        $this->app->response->setBody($this->app->serializer->serialize($articleCategories, 'json'));
    }

    public function getArticleCategoryByIdAction($id) {
        $entityManager              = $this->app->entityManager;
        $articleCategoryRepository  = $entityManager->getRepository(self::$ARTICLE_FULL_QUALIFIED_CLASSNAME);
        $articleCategory            = $articleCategoryRepository->findOneBy(array('id' => $id));

        if ($articleCategory === null) {
            $this->app->response->setStatus(HttpStatusCodes::NOT_FOUND);
            return;
        }

        $this->app->response->header('Content-Type', 'application/json');
        $this->app->response->setStatus(HttpStatusCodes::OK);
        $this->app->response->setBody($this->app->serializer->serialize($articleCategory, 'json'));
    }

    public function updateArticleCategoryAction($articleCategoryId) {
        $articleCategoryObj         = $this->app->serializer->deserialize($this->app->request->getBody(), self::$ARTICLE_FULL_QUALIFIED_CLASSNAME, 'json');

        // get original article
        $entityManager              = $this->app->entityManager;
        $articleCategoryRepository  = $entityManager->getRepository(self::$ARTICLE_FULL_QUALIFIED_CLASSNAME);
        $origArticleCategory        = $articleCategoryRepository->findOneBy(array('id' => $articleCategoryId));

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

        $this->app->response->header('Content-Type', 'application/json');
        $this->app->response->setStatus(HttpStatusCodes::OK);
        $this->app->response->setBody($this->app->serializer->serialize($origArticleCategory, 'json'));
    }

    public function insertArticleAction() {
        $articleCategoryObj      = $this->app->serializer->deserialize($this->app->request->getBody(), self::$ARTICLE_FULL_QUALIFIED_CLASSNAME, 'json');

        $entityManager           = $this->app->entityManager;
        $entityManager->persist($articleCategoryObj);

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
        $this->app->response->setBody($this->app->serializer->serialize($articleCategoryObj, 'json'));
    }

    public function deleteArticleCategoryByIdAction($id) {
        $entityManager              = $this->app->entityManager;
        $articleCategoryRepository  = $entityManager->getRepository(self::$ARTICLE_FULL_QUALIFIED_CLASSNAME);
        $articleCategory            = $articleCategoryRepository->findOneBy(array('id' => $id));

        if ($articleCategory === null) {
            $this->app->response->setStatus(HttpStatusCodes::NOT_FOUND);
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