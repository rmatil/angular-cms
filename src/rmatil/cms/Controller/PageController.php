<?php

namespace rmatil\cms\Controller;

use SlimController\SlimController;
use rmatil\cms\Constants\HttpStatusCodes;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Entities\Page;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\DBALException;
use Doctrine\Common\Collections\ArrayCollection;
use DateTime;

class PageController extends SlimController {

    public function getPagesAction() {
        $entityManager   = $this->app->entityManager;
        $pageRepository  = $entityManager->getRepository(EntityNames::PAGE);
        $pages           = $pageRepository->findAll();

        $this->app->expires(0);
        $this->app->response->header('Content-Type', 'application/json');
        $this->app->response->setStatus(HttpStatusCodes::OK);
        $this->app->response->setBody($this->app->serializer->serialize($pages, 'json'));
    }

    public function getPageByIdAction($id) {
        $entityManager   = $this->app->entityManager;
        $pageRepository  = $entityManager->getRepository(EntityNames::PAGE);
        $page            = $pageRepository->findOneBy(array('id' => $id));

        if ($page === null) {
            $this->app->response->setStatus(HttpStatusCodes::NOT_FOUND);
            return;
        }

        // do not show lock if requested by the same user as currently locked
        if ($page->getIsLockedBy() !== null &&
            $page->getIsLockedBy()->getId() === $_SESSION['user_id']) {
            $page->setIsLockedBy(null);
        }

        $userRepository             = $entityManager->getRepository(EntityNames::USER);
        $origUser                   = $userRepository->findOneBy(array('id' => $_SESSION['user_id']));
        $page->setAuthor($origUser);

        $this->app->expires(0);
        $this->app->response->header('Content-Type', 'application/json');
        $this->app->response->setStatus(HttpStatusCodes::OK);
        $this->app->response->setBody($this->app->serializer->serialize($page, 'json'));

        // set requesting user as lock
        $page->setIsLockedBy($origUser);

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

    public function updatePageAction($pageId) {
        $pageObject                 = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::PAGE, 'json');

        $now                        = new DateTime();
        $pageObject->setLastEditDate($now);

        // get original page
        $entityManager              = $this->app->entityManager;
        $pageRepository             = $entityManager->getRepository(EntityNames::PAGE);
        $origPage                   = $pageRepository->findOneBy(array('id' => $pageId));

        $languageRepository         = $entityManager->getRepository(EntityNames::LANGUAGE);
        $origLanguage               = $languageRepository->findOneBy(array('id' => $pageObject->getLanguage()->getId()));
        $pageObject->setLanguage($origLanguage);

        $userRepository             = $entityManager->getRepository(EntityNames::USER);
        $origUser                   = $userRepository->findOneBy(array('id' => $_SESSION['user_id']));
        $pageObject->setAuthor($origUser);

        $pageCategoryRepository     = $entityManager->getRepository(EntityNames::PAGE_CATEGORY);
        $origPageCategory           = $pageCategoryRepository->findOneBy(array('id' => $origPage->getCategory()->getId()));
        $pageObject->setCategory($origPageCategory);

        // get all articles
        $articleRepository          = $entityManager->getRepository(EntityNames::ARTICLE);
        $origArticles               = new ArrayCollection();

        // remove association of this page from each article
        foreach ($origPage->getArticles()->toArray() as $article) {
            $origArticle = $articleRepository->findOneBy(array('id' => $article->getId()));
            $origArticle->setPage(null);
        }
        $origPage->setArticles(null);

        // force update
        try {
            $entityManager->flush();
        } catch (DBALException $dbalex) {
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $dbalex->getMessage()));
            $this->app->response->setStatus(HttpStatusCodes::CONFLICT);
            return;
        }

        // add all selected articles
        foreach ($pageObject->getArticles()->toArray() as $article) {
            if ($article->getId() === null) {
                // may occur in case the article was removed from the collection by angular
                continue;
            }

            $origArticle = $articleRepository->findOneBy(array('id' => $article->getId()));
            $origArticle->setPage($origPage);
            $origArticles->add($origArticle);
        }
        $pageObject->setArticles($origArticles);

        $origPage->update($pageObject);
        // release lock on editing
        $origPage->setIsLockedBy(null);

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
        $this->app->response->setBody($this->app->serializer->serialize($origPage, 'json'));
    }

    public function insertPageAction() {
        $pageObject                 = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::PAGE, 'json');

        // set now as creation date
        $now                        = new DateTime();
        $pageObject->setLastEditDate($now);
        $pageObject->setCreationDate($now);

        $entityManager              = $this->app->entityManager;
        $languageRepository         = $entityManager->getRepository(EntityNames::LANGUAGE);
        $origLanguage               = $languageRepository->findOneBy(array('id' => $pageObject->getLanguage()->getId()));
        $pageObject->setLanguage($origLanguage);

        $userRepository             = $entityManager->getRepository(EntityNames::USER);
        $origUser                   = $userRepository->findOneBy(array('id' => $_SESSION['user_id']));
        $pageObject->setAuthor($origUser);

        $pageCategoryRepository     = $entityManager->getRepository(EntityNames::PAGE_CATEGORY);
        $origPageCategory           = $pageCategoryRepository->findOneBy(array('id' => $pageObject->getCategory()->getId()));
        $pageObject->setCategory($origPageCategory);

        $origArticles               = new ArrayCollection();
        $articleRepository          = $entityManager->getRepository(EntityNames::ARTICLE);
        // get origArticles
        foreach ($pageObject->getArticles()->toArray() as $article) {
            $origArticle = $articleRepository->findOneBy(array('id' => $article->getId()));
            $origArticles->add($origArticle);
        }
        $pageObject->setArticles($origArticles);

        $entityManager              = $this->app->entityManager;
        $entityManager->persist($pageObject);

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
        $this->app->response->setBody($this->app->serializer->serialize($pageObject, 'json'));
    }

    public function deletePageByIdAction($id) {
        $entityManager          = $this->app->entityManager;
        $pageRepository         = $entityManager->getRepository(EntityNames::PAGE);
        $page                   = $pageRepository->findOneBy(array('id' => $id));

        if ($page === null) {
            $this->app->response->setStatus(HttpStatusCodes::NOT_FOUND);
            return;
        }

        $articleRepository = $entityManager->getRepository(EntityNames::ARTICLE);

        // remove all corresponding articles
        foreach ($page->getArticles() as $article) {
            $origArticle = $articleRepository->findOneBy(array('id' => $article->getId()));
            $origArticle->setPage(null);
        }

        $entityManager->remove($page);

        try {
            $entityManager->flush();
        } catch (DBALException $dbalex) {
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $dbalex->getMessage()));
            $this->app->response->setStatus(HttpStatusCodes::CONFLICT);
            return;
        }

        $this->app->response->setStatus(HttpStatusCodes::NO_CONTENT);
    }

    public function getEmptyPageAction() {
        $page = new Page();

        $userRepository = $this->app->entityManager->getRepository(EntityNames::USER);
        $origUser       = $userRepository->findOneBy(array('id' => $_SESSION['user_id']));
        $page->setAuthor($origUser);

        $now = new DateTime();
        $page->setCreationDate($now);
        $page->setLastEditDate($now);

        $this->app->response->header('Content-Type', 'application/json');
        $this->app->response->setStatus(HttpStatusCodes::OK);
        $this->app->response->setBody($this->app->serializer->serialize($page, 'json'));
    }

}