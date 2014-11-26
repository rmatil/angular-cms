<?php

namespace rmatil\cms\Controller;

use SlimController\SlimController;
use rmatil\cms\Constants\HttpStatusCodes;
use rmatil\cms\Entities\Article;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\DBALException;
use DateTime;

class ArticleController extends SlimController {

    private static $ARTICLE_FULL_QUALIFIED_CLASSNAME            = 'rmatil\cms\Entities\Article';
    private static $LANGUAGE_FULL_QUALIFIED_CLASSNAME           = 'rmatil\cms\Entities\Language';
    private static $USER_FULL_QUALIFIED_CLASSNAME               = 'rmatil\cms\Entities\User';
    private static $ARTICLE_CATEGORY_FULL_QUALIFIED_CLASSNAME   = 'rmatil\cms\Entities\ArticleCategory';

    public function getArticlesAction() {
        $entityManager      = $this->app->entityManager;
        $articleRepository  = $entityManager->getRepository(self::$ARTICLE_FULL_QUALIFIED_CLASSNAME);
        $articles           = $articleRepository->findAll();

        $this->app->response->header('Content-Type', 'application/json');
        $this->app->response->setStatus(HttpStatusCodes::OK);
        $this->app->response->setBody($this->app->serializer->serialize($articles, 'json'));
    }

    public function getArticleByIdAction($id) {
        $entityManager      = $this->app->entityManager;
        $articleRepository  = $entityManager->getRepository(self::$ARTICLE_FULL_QUALIFIED_CLASSNAME);
        $article            = $articleRepository->findOneBy(array('id' => $id));

        if ($article === null) {
            $this->app->response->setStatus(HttpStatusCodes::NOT_FOUND);
            return;
        }

        // do not show lock if requested by the same user as currently locked
        if ($article->getIsLockedBy() !== null &&
            $article->getIsLockedBy()->getId() === $_SESSION['user']->getId()) {
            $article->setIsLockedBy(null);
        }

        $userRepository             = $entityManager->getRepository(self::$USER_FULL_QUALIFIED_CLASSNAME);
        $origUser                   = $userRepository->findOneBy(array('id' => $_SESSION['user']->getId()));
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
        $articleObject              = $this->app->serializer->deserialize($this->app->request->getBody(), self::$ARTICLE_FULL_QUALIFIED_CLASSNAME, 'json');

        // set now as edit date
        $now                        = new DateTime();
        $articleObject->setLastEditDate($now);

        // TODO: what if an user simultaneously deleted this article?
        // -> insert it again? 

        // get original article
        $entityManager              = $this->app->entityManager;
        $articleRepository          = $entityManager->getRepository(self::$ARTICLE_FULL_QUALIFIED_CLASSNAME);
        $origArticle                = $articleRepository->findOneBy(array('id' => $articleId));

        $languageRepository         = $entityManager->getRepository(self::$LANGUAGE_FULL_QUALIFIED_CLASSNAME);
        $origLanguage               = $languageRepository->findOneBy(array('id' => $articleObject->getLanguage()->getId()));
        $articleObject->setLanguage($origLanguage);

        $userRepository             = $entityManager->getRepository(self::$USER_FULL_QUALIFIED_CLASSNAME);
        $origUser                   = $userRepository->findOneBy(array('id' => $_SESSION['user']->getId()));
        $articleObject->setAuthor($origUser);

        $articleCategoryRepository  = $entityManager->getRepository(self::$ARTICLE_CATEGORY_FULL_QUALIFIED_CLASSNAME);
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
        $articleObject      = $this->app->serializer->deserialize($this->app->request->getBody(), self::$ARTICLE_FULL_QUALIFIED_CLASSNAME, 'json');

        // set now as creation date
        $now                = new DateTime();
        $articleObject->setLastEditDate($now);
        $articleObject->setCreationDate($now);

        $entityManager              = $this->app->entityManager;
        $userRepository             = $entityManager->getRepository(self::$USER_FULL_QUALIFIED_CLASSNAME);
        $origUser                   = $userRepository->findOneBy(array('id' => $_SESSION['user']->getId()));
        $articleObject->setAuthor($origUser);

        $languageRepository         = $entityManager->getRepository(self::$LANGUAGE_FULL_QUALIFIED_CLASSNAME);
        $origLanguage               = $languageRepository->findOneBy(array('id' => $articleObject->getLanguage()->getId()));
        $articleObject->setLanguage($origLanguage);

        $articleCategoryRepository  = $entityManager->getRepository(self::$ARTICLE_CATEGORY_FULL_QUALIFIED_CLASSNAME);
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
        $articleRepository  = $entityManager->getRepository(self::$ARTICLE_FULL_QUALIFIED_CLASSNAME);
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
        
        $userRepository             = $this->app->entityManager->getRepository(self::$USER_FULL_QUALIFIED_CLASSNAME);
        $origUser                   = $userRepository->findOneBy(array('id' => $_SESSION['user']->getId()));
        $article->setAuthor($origUser);
        
        $now = new DateTime();
        $article->setCreationDate($now);
        $article->setLastEditDate($now);

        $this->app->response->header('Content-Type', 'application/json');
        $this->app->response->setStatus(HttpStatusCodes::OK);
        $this->app->response->setBody($this->app->serializer->serialize($article, 'json'));
    }
}