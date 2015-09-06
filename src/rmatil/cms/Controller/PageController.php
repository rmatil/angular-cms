<?php

namespace rmatil\cms\Controller;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\DBALException;
use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Constants\HttpStatusCodes;
use rmatil\cms\Entities\Language;
use rmatil\cms\Entities\Page;
use rmatil\cms\Entities\PageCategory;
use rmatil\cms\Entities\User;
use rmatil\cms\Response\ResponseFactory;
use SlimController\SlimController;

/**
 * @package rmatil\cms\Controller
 */
class PageController extends SlimController {

    public function getPagesAction() {
        $pageRepository = $this->app->entityManager->getRepository(EntityNames::PAGE);
        $pages = $pageRepository->findAll();

        ResponseFactory::createJsonResponse($this->app, $pages);
    }

    public function getPageByIdAction($id) {
        $entityManager = $this->app->entityManager;
        $pageRepository = $entityManager->getRepository(EntityNames::PAGE);
        $page = $pageRepository->findOneBy(array('id' => $id));

        if ( ! ($page instanceof Page)) {
            ResponseFactory::createNotFoundResponse($this->app);
            return;
        }

        // do not show lock if requested by the same user as currently locked
        if (($page->getIsLockedBy() instanceof User) &&
            $page->getIsLockedBy()->getId() === $_SESSION['user_id']
        ) {
            $page->setIsLockedBy(null);
        }

        $userRepository = $entityManager->getRepository(EntityNames::USER);
        $origUser = $userRepository->findOneBy(array('id' => $_SESSION['user_id']));
        $page->setAuthor($origUser);

        ResponseFactory::createJsonResponse($this->app, $page);

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
        /** @var \rmatil\cms\Entities\Page $pageObject */
        $pageObject = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::PAGE, 'json');

        $now = new DateTime();
        $pageObject->setLastEditDate($now);

        // get original page
        /** @var \Doctrine\ORM\EntityManager $entityManager */
        $entityManager = $this->app->entityManager;
        $pageRepository = $entityManager->getRepository(EntityNames::PAGE);
        $origPage = $pageRepository->findOneBy(array('id' => $pageId));

        if ( ! ($origPage instanceof Page)) {
            ResponseFactory::createNotFoundResponse($this->app);
            return;
        }

        if ($pageObject->getLanguage() instanceof Language) {
            $languageRepository = $entityManager->getRepository(EntityNames::LANGUAGE);
            $origLanguage = $languageRepository->findOneBy(array('id' => $pageObject->getLanguage()->getId()));
            $pageObject->setLanguage($origLanguage);
        }

        if ($origPage->getCategory() instanceof PageCategory) {
            $pageCategoryRepository = $entityManager->getRepository(EntityNames::PAGE_CATEGORY);
            $origPageCategory = $pageCategoryRepository->findOneBy(array('id' => $origPage->getCategory()->getId()));
            $pageObject->setCategory($origPageCategory);
        }

        $userRepository = $entityManager->getRepository(EntityNames::USER);
        $origUser = $userRepository->findOneBy(array('id' => $_SESSION['user_id']));
        $pageObject->setAuthor($origUser);

        // get all allowed usergroups
        $userGroupRepo = $entityManager->getRepository(EntityNames::USER_GROUP);
        $allUserGroups = $userGroupRepo->findAll();

        foreach ($allUserGroups as $userGroup) {
            if ($userGroup->getAccessiblePages()->contains($origPage) &&
                ! $origPage->getAllowedUserGroups()->contains($userGroup)
            ) {
                // maintain inverse side
                $origPage->addAllowedUserGroup($userGroup);
            } else if ( ! $userGroup->getAccessiblePages()->contains($origPage) &&
                $origPage->getAllowedUserGroups()->contains($userGroup)
            ) {
                // maintain inverse side
                $origPage->removeAllowedUserGroup($userGroup);
            }

            if ( ! $userGroup->getAccessiblePages()->contains($origPage) &&
                ! $origPage->getAllowedUserGroups()->contains($userGroup)
            ) {
                // use this loop here, as contains() does not
                // consider a proxy object as a equally object. Basically, it isn't...
                foreach ($pageObject->getAllowedUserGroups() as $userGroupObj) {
                    if ($userGroupObj->getId() === $userGroup->getId()) {
                        // usergroup was selected and we can add the page to the accessible usergroups
                        // and the usergroup as allowedUserGroup to the page (inside addAccessiblePage-Method)
                        $userGroup->addAccessiblePage($origPage);
                        break;
                    }
                }

            } else if ($userGroup->getAccessiblePages()->contains($origPage) &&
                $origPage->getAllowedUserGroups()->contains($userGroup) &&
                ! $pageObject->getAllowedUserGroups()->contains($userGroup)
            ) {
                $doesContainObj = false;
                foreach ($pageObject->getAllowedUserGroups() as $userGroupObj) {
                    if ($userGroupObj->getId() === $userGroup->getId()) {
                        $doesContainObj = true;
                        break;
                    }
                }

                if ( ! $doesContainObj) {
                    // usegroup was unselected and we can remove the page from the accessible usergroups
                    // and the usergroup as the allowedUserGroup from the page (inside removeAccessiblePage)
                    $userGroup->removeAccessiblePage($origPage);
                }
            }
        }

        // get all articles
        $articleRepository = $entityManager->getRepository(EntityNames::ARTICLE);
        $origArticles = new ArrayCollection();

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

        $origPage->setArticles($pageObject->getArticles());
        $origPage->setAuthor($pageObject->getAuthor());
        $origPage->setCategory($pageObject->getCategory());
        $origPage->setLanguage($pageObject->getLanguage());
        $origPage->setParent($pageObject->getParent());
        $origPage->setTitle($pageObject->getTitle());
        $origPage->setCreationDate($pageObject->getCreationDate());
        $origPage->setHasSubnavigation($pageObject->getHasSubnavigation());
        $origPage->setIsPublished($pageObject->getIsPublished());
        $origPage->setUrlName($pageObject->getUrlName());
        $origPage->setLastEditDate($pageObject->getLastEditDate());
        $origPage->setIsStartPage($pageObject->getIsStartPage());

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

        ResponseFactory::createJsonResponse($this->app, $origPage);
    }

    public function insertPageAction() {
        /** @var \rmatil\cms\Entities\Page $pageObject */
        $pageObject = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::PAGE, 'json');

        // set now as creation date
        $now = new DateTime();
        $pageObject->setLastEditDate($now);
        $pageObject->setCreationDate($now);

        $entityManager = $this->app->entityManager;

        if ($pageObject->getLanguage() instanceof Language) {
            $languageRepository = $entityManager->getRepository(EntityNames::LANGUAGE);
            $origLanguage = $languageRepository->findOneBy(array('id' => $pageObject->getLanguage()->getId()));
            $pageObject->setLanguage($origLanguage);
        }

        if ($pageObject->getCategory() instanceof PageCategory) {
            $pageCategoryRepository = $entityManager->getRepository(EntityNames::PAGE_CATEGORY);
            $origPageCategory = $pageCategoryRepository->findOneBy(array('id' => $pageObject->getCategory()->getId()));
            $pageObject->setCategory($origPageCategory);
        }

        $userRepository = $entityManager->getRepository(EntityNames::USER);
        $origUser = $userRepository->findOneBy(array('id' => $_SESSION['user_id']));
        $pageObject->setAuthor($origUser);

        $origArticles = new ArrayCollection();
        $articleRepository = $entityManager->getRepository(EntityNames::ARTICLE);
        // get origArticles
        foreach ($pageObject->getArticles()->toArray() as $article) {
            /** @var \rmatil\cms\Entities\Article $origArticle */
            $origArticle = $articleRepository->findOneBy(array('id' => $article->getId()));
            $origArticle->setPage($pageObject);
            $origArticles->add($origArticle);
        }
        $pageObject->setArticles($origArticles);


        // get all allowed usergroups
        $userGroupObjs = $pageObject->getAllowedUserGroups()->toArray(); // use array here, otherwise this reference will also be empty after clear()
        $pageObject->getAllowedUserGroups()->clear();
        $userGroupRepo = $entityManager->getRepository(EntityNames::USER_GROUP);
        $allUserGroups = $userGroupRepo->findAll();

        foreach ($allUserGroups as $userGroup) {
            foreach ($userGroupObjs as $userGroupObj) {
                if ($userGroupObj->getId() === $userGroup->getId()) {
                    // usergroup was selected and we can add the article to the accessible usergroups
                    // and the usergroup as allowedUserGroup to the article (inside addAccessibleArticle-Method)
                    $userGroup->addAccessiblePage($pageObject);
                    break;
                }
            }
        }

        $entityManager = $this->app->entityManager;
        $entityManager->persist($pageObject);

        try {
            $entityManager->flush();
        } catch (DBALException $dbalex) {
            $now = new DateTime();
            $this->app->log->error(sprintf('[%s]: %s', $now->format('d-m-Y H:i:s'), $dbalex->getMessage()));
            $this->app->response->setStatus(HttpStatusCodes::CONFLICT);
            return;
        }

        ResponseFactory::createJsonResponseWithCode($this->app, HttpStatusCodes::CREATED, $pageObject);
    }

    public function deletePageByIdAction($id) {
        $entityManager = $this->app->entityManager;
        $pageRepository = $entityManager->getRepository(EntityNames::PAGE);
        $page = $pageRepository->findOneBy(array('id' => $id));

        if ( ! ($page instanceof Page)) {
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
        $origUser = $userRepository->findOneBy(array('id' => $_SESSION['user_id']));
        $page->setAuthor($origUser);

        $now = new DateTime();
        $page->setCreationDate($now);
        $page->setLastEditDate($now);

        ResponseFactory::createJsonResponse($this->app, $page);
    }

}