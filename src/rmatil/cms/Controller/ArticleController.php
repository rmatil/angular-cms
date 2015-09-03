<?php

namespace rmatil\cms\Controller;

use DateTime;
use Doctrine\DBAL\DBALException;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Constants\HttpStatusCodes;
use rmatil\cms\Entities\Article;
use rmatil\cms\Entities\ArticleCategory;
use rmatil\cms\Entities\Language;
use rmatil\cms\Entities\User;
use rmatil\cms\Response\ResponseFactory;
use SlimController\SlimController;

/**
 * @package rmatil\cms\Controller
 */
class ArticleController extends SlimController {

    public function getArticlesAction() {
        $articleRepository = $this->app->entityManager->getRepository(EntityNames::ARTICLE);
        $articles = $articleRepository->findAll();

        ResponseFactory::createJsonResponse($this->app, $articles);
    }

    public function getArticleByIdAction($id) {
        $entityManager = $this->app->entityManager;
        $articleRepository = $entityManager->getRepository(EntityNames::ARTICLE);
        $article = $articleRepository->findOneBy(array('id' => $id));

        if ( ! ($article instanceof Article)) {
            ResponseFactory::createNotFoundResponse($this->app);
            return;
        }

        // do not show lock if requested by the same user as currently locked
        if ($article->getIsLockedBy() instanceof User &&
            $article->getIsLockedBy()->getId() === $_SESSION['user_id']
        ) {
            $article->setIsLockedBy(null);
        }

        $userRepository = $entityManager->getRepository(EntityNames::USER);
        $origUser = $userRepository->findOneBy(array('id' => $_SESSION['user_id']));
        $article->setAuthor($origUser);

        ResponseFactory::createJsonResponse($this->app, $article);

        // set requesting user as lock
        $article->setIsLockedBy($origUser);

        // force update
        try {
            $entityManager->flush();
        } catch (DBALException $dbalex) {
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $dbalex->getMessage()));
            $this->app->response->setStatus(HttpStatusCodes::CONFLICT);
            return;
        }
    }

    public function updateArticleAction($articleId) {
        /** @var \rmatil\cms\Entities\Article $articleObject */
        $articleObject = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::ARTICLE, 'json');

        // set now as edit date
        $now = new DateTime();
        $articleObject->setLastEditDate($now);

        // get original article
        $entityManager = $this->app->entityManager;
        $articleRepository = $entityManager->getRepository(EntityNames::ARTICLE);
        $origArticle = $articleRepository->findOneBy(array('id' => $articleId));

        if ( ! ($origArticle instanceof Article)) {
            ResponseFactory::createNotFoundResponse($this->app);
            return;
        }

        if ($articleObject->getLanguage() instanceof Language) {
            $languageRepository = $entityManager->getRepository(EntityNames::LANGUAGE);
            $origLanguage = $languageRepository->findOneBy(array('id' => $articleObject->getLanguage()->getId()));
            $articleObject->setLanguage($origLanguage);
        }

        $userRepository = $entityManager->getRepository(EntityNames::USER);
        $origUser = $userRepository->findOneBy(array('id' => $_SESSION['user_id']));
        $articleObject->setAuthor($origUser);

        if ($articleObject->getCategory() instanceof ArticleCategory) {
            $articleCategoryRepository = $entityManager->getRepository(EntityNames::ARTICLE_CATEGORY);
            $origArticleCategory = $articleCategoryRepository->findOneBy(array('id' => $articleObject->getCategory()->getId()));
            $articleObject->setCategory($origArticleCategory);
        }

        $origArticle->update($articleObject);
        // release lock on editing
        $origArticle->setIsLockedBy(null);

        // force update
        try {
            $entityManager->flush();
        } catch (DBALException $dbalex) {
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $dbalex->getMessage()));
            $this->app->response->setStatus(HttpStatusCodes::CONFLICT);
            return;
        }

        ResponseFactory::createJsonResponse($this->app, $origArticle);
    }

    public function insertArticleAction() {
        /** @var \rmatil\cms\Entities\Article $articleObject */
        $articleObject = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::ARTICLE, 'json');

        // set now as creation date
        $now = new DateTime();
        $articleObject->setLastEditDate($now);
        $articleObject->setCreationDate($now);

        $entityManager = $this->app->entityManager;
        $userRepository = $entityManager->getRepository(EntityNames::USER);
        $origUser = $userRepository->findOneBy(array('id' => $_SESSION['user_id']));
        $articleObject->setAuthor($origUser);

        if ($articleObject->getLanguage() instanceof Language) {
            $languageRepository = $entityManager->getRepository(EntityNames::LANGUAGE);
            $origLanguage = $languageRepository->findOneBy(array('id' => $articleObject->getLanguage()->getId()));
            $articleObject->setLanguage($origLanguage);
        }

        if ($articleObject->getCategory() instanceof ArticleCategory) {
            $articleCategoryRepository = $entityManager->getRepository(EntityNames::ARTICLE_CATEGORY);
            $origArticleCategory = $articleCategoryRepository->findOneBy(array('id' => $articleObject->getCategory()->getId()));
            $articleObject->setCategory($origArticleCategory);
        }

        $entityManager = $this->app->entityManager;
        $entityManager->persist($articleObject);

        try {
            $entityManager->flush();
        } catch (DBALException $dbalex) {
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $dbalex->getMessage()));
            $this->app->response->setStatus(HttpStatusCodes::CONFLICT);
            return;
        }

        ResponseFactory::createJsonResponseWithCode($this->app, HttpStatusCodes::CREATED, $articleObject);
    }

    public function deleteArticleByIdAction($id) {
        $entityManager = $this->app->entityManager;
        $articleRepository = $entityManager->getRepository(EntityNames::ARTICLE);
        $article = $articleRepository->findOneBy(array('id' => $id));

        if ( ! ($article instanceof Article)) {
            $this->app->response->setStatus(HttpStatusCodes::NOT_FOUND);
            return;
        }

        // prevent conflict on foreign key constraint
        $article->setIsLockedBy(null);

        $entityManager->remove($article);

        try {
            $entityManager->flush();
        } catch (DBALException $dbalex) {
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $dbalex->getMessage()));
            $this->app->response->setStatus(HttpStatusCodes::CONFLICT);
        }

        $this->app->response->setStatus(HttpStatusCodes::NO_CONTENT);
    }

    public function getEmptyArticleAction() {
        $article = new Article();

        $userRepository = $this->app->entityManager->getRepository(EntityNames::USER);
        $origUser = $userRepository->findOneBy(array('id' => $_SESSION['user_id']));
        $article->setAuthor($origUser);

        $now = new DateTime();
        $article->setCreationDate($now);
        $article->setLastEditDate($now);

        ResponseFactory::createJsonResponse($this->app, $article);
    }
}