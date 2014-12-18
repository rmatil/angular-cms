<?php

namespace rmatil\cms\Controller;

use SlimController\SlimController;
use rmatil\cms\Constants\HttpStatusCodes;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Entities\Article;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\DBALException;
use DateTime;

class ArticleController extends SlimController {

    public function getArticlesAction() {
        $entityManager      = $this->app->entityManager;
        $articleRepository  = $entityManager->getRepository(EntityNames::ARTICLE);
        $articles           = $articleRepository->findAll();

        $this->app->response->header('Content-Type', 'application/json');
        $this->app->response->setStatus(HttpStatusCodes::OK);
        $this->app->response->setBody($this->app->serializer->serialize($articles, 'json'));
    }

    public function getArticleByIdAction($id) {
        $entityManager      = $this->app->entityManager;
        $articleRepository  = $entityManager->getRepository(EntityNames::ARTICLE);
        $article            = $articleRepository->findOneBy(array('id' => $id));

        if ($article === null) {
            $this->app->response->setStatus(HttpStatusCodes::NOT_FOUND);
            return;
        }

        // do not show lock if requested by the same user as currently locked
        if ($article->getIsLockedBy() !== null &&
            $article->getIsLockedBy()->getId() === $_SESSION['user_id']) {
            $article->setIsLockedBy(null);
        }

        $userRepository             = $entityManager->getRepository(EntityNames::USER);
        $origUser                   = $userRepository->findOneBy(array('id' => $_SESSION['user_id']));
        $article->setAuthor($origUser);

        $this->app->response->header('Content-Type', 'application/json');
        $this->app->response->setStatus(HttpStatusCodes::OK);
        $this->app->response->setBody($this->app->serializer->serialize($article, 'json'));

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
        $articleObject              = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::ARTICLE, 'json');

        // set now as edit date
        $now                        = new DateTime();
        $articleObject->setLastEditDate($now);

        // TODO: what if an user simultaneously deleted this article?
        // -> insert it again? 

        // get original article
        $entityManager              = $this->app->entityManager;
        $articleRepository          = $entityManager->getRepository(EntityNames::ARTICLE);
        $origArticle                = $articleRepository->findOneBy(array('id' => $articleId));

        $languageRepository         = $entityManager->getRepository(EntityNames::LANGUAGE);
        $origLanguage               = $languageRepository->findOneBy(array('id' => $articleObject->getLanguage()->getId()));
        $articleObject->setLanguage($origLanguage);

        $userRepository             = $entityManager->getRepository(EntityNames::USER);
        $origUser                   = $userRepository->findOneBy(array('id' => $_SESSION['user_id']));
        $articleObject->setAuthor($origUser);

        $articleCategoryRepository  = $entityManager->getRepository(EntityNames::ARTICLE_CATEGORY);
        $origArticleCategory        = $articleCategoryRepository->findOneBy(array('id' => $articleObject->getCategory()->getId()));
        $articleObject->setCategory($origArticleCategory);

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

        $this->app->response->header('Content-Type', 'application/json');
        $this->app->response->setStatus(HttpStatusCodes::OK);
        $this->app->response->setBody($this->app->serializer->serialize($origArticle, 'json'));
    }

    public function insertArticleAction() {
        $articleObject      = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::ARTICLE, 'json');

        // set now as creation date
        $now                = new DateTime();
        $articleObject->setLastEditDate($now);
        $articleObject->setCreationDate($now);

        $entityManager              = $this->app->entityManager;
        $userRepository             = $entityManager->getRepository(EntityNames::USER);
        $origUser                   = $userRepository->findOneBy(array('id' => $_SESSION['user_id']));
        $articleObject->setAuthor($origUser);

        $languageRepository         = $entityManager->getRepository(EntityNames::LANGUAGE);
        $origLanguage               = $languageRepository->findOneBy(array('id' => $articleObject->getLanguage()->getId()));
        $articleObject->setLanguage($origLanguage);

        $articleCategoryRepository  = $entityManager->getRepository(EntityNames::ARTICLE_CATEGORY);
        $origArticleCategory        = $articleCategoryRepository->findOneBy(array('id' => $articleObject->getCategory()->getId()));
        $articleObject->setCategory($origArticleCategory);

        $entityManager              = $this->app->entityManager;
        $entityManager->persist($articleObject);

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
        $this->app->response->setBody($this->app->serializer->serialize($articleObject, 'json'));
    }

    public function deleteArticleByIdAction($id) {
        $entityManager      = $this->app->entityManager;
        $articleRepository  = $entityManager->getRepository(EntityNames::ARTICLE);
        $article            = $articleRepository->findOneBy(array('id' => $id));

        if ($article === null) {
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
        
        $userRepository             = $this->app->entityManager->getRepository(EntityNames::USER);
        $origUser                   = $userRepository->findOneBy(array('id' => $_SESSION['user_id']));
        $article->setAuthor($origUser);
        
        $now = new DateTime();
        $article->setCreationDate($now);
        $article->setLastEditDate($now);

        $this->app->response->header('Content-Type', 'application/json');
        $this->app->response->setStatus(HttpStatusCodes::OK);
        $this->app->response->setBody($this->app->serializer->serialize($article, 'json'));
    }
}