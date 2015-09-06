<?php

namespace rmatil\cms\Controller;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
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

        // get all allowed usergroups
        // first clean all allowed user groups to remove the unselected ones
        $userGroupRepo = $entityManager->getRepository(EntityNames::USER_GROUP);
        $allUserGroups = $userGroupRepo->findAll();

        foreach ($allUserGroups as $userGroup) {
            if ($userGroup->getAccessibleArticles()->contains($origArticle) &&
                ! $origArticle->getAllowedUserGroups()->contains($userGroup)
            ) {
                // maintain inverse side
                $origArticle->addAllowedUserGroup($userGroup);
            } else if ( ! $userGroup->getAccessibleArticles()->contains($origArticle) &&
                $origArticle->getAllowedUserGroups()->contains($userGroup)
            ) {
                // maintain inverse side
                $origArticle->removeAllowedUserGroup($userGroup);
            }

            if ( ! $userGroup->getAccessibleArticles()->contains($origArticle) &&
                ! $origArticle->getAllowedUserGroups()->contains($userGroup)
            ) {
                // use this loop here, as contains() does not
                // consider a proxy object as a equally object. Basically, it isn't...
                foreach ($articleObject->getAllowedUserGroups() as $userGroupObj) {
                    if ($userGroupObj->getId() === $userGroup->getId()) {
                        // usergroup was selected and we can add the article to the accessible usergroups
                        // and the usergroup as allowedUserGroup to the article (inside addAccessibleArticle-Method)
                        $userGroup->addAccessibleArticle($origArticle);
                        break;
                    }
                }

            } else if ($userGroup->getAccessibleArticles()->contains($origArticle) &&
                $origArticle->getAllowedUserGroups()->contains($userGroup) &&
                ! $articleObject->getAllowedUserGroups()->contains($userGroup)
            ) {
                $doesContainObj = false;
                foreach ($articleObject->getAllowedUserGroups() as $userGroupObj) {
                    if ($userGroupObj->getId() === $userGroup->getId()) {
                        $doesContainObj = true;
                        break;
                    }
                }

                if ( ! $doesContainObj) {
                    // usegroup was unselected and we can remove the article from the accessible usergroups
                    // and the usergroup as the allowedUserGroup from the article (inside removeAccessibleArticle)
                    $userGroup->removeAccessibleArticle($origArticle);
                }
            }
        }

        $origArticle->setTitle($articleObject->getTitle());
        $origArticle->setContent($articleObject->getContent());
        $origArticle->setUrlName($articleObject->getUrlName());
        $origArticle->setIsPublished($articleObject->getIsPublished());
        $origArticle->setCategory($articleObject->getCategory());
        $origArticle->setAuthor($articleObject->getAuthor());
        $origArticle->setLanguage($articleObject->getLanguage());
        $origArticle->setLastEditDate($articleObject->getLastEditDate());
        $origArticle->setCreationDate($articleObject->getCreationDate());
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

        // get all allowed usergroups
        $userGroupObjs = $articleObject->getAllowedUserGroups()->toArray(); // use array here, otherwise this reference will also be empty after clear()
        $articleObject->getAllowedUserGroups()->clear();
        $userGroupRepo = $entityManager->getRepository(EntityNames::USER_GROUP);
        $allUserGroups = $userGroupRepo->findAll();

        foreach ($allUserGroups as $userGroup) {
            foreach ($userGroupObjs as $userGroupObj) {
                if ($userGroupObj->getId() === $userGroup->getId()) {
                    // usergroup was selected and we can add the article to the accessible usergroups
                    // and the usergroup as allowedUserGroup to the article (inside addAccessibleArticle-Method)
                    $userGroup->addAccessibleArticle($articleObject);
                    break;
                }
            }
        }

        /** @var \Doctrine\ORM\EntityManager $entityManager */
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