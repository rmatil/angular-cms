<?php

namespace rmatil\cms\Controller;

use rmatil\cms\Constants\EntityNames;
use rmatil\cms\Entities\Article;
use rmatil\cms\Entities\Event;
use rmatil\cms\Entities\Page;
use rmatil\cms\Entities\User;
use SlimController\SlimController;

/**
 * @package rmatil\cms\Controller
 */
class IndexController extends SlimController {

    public function indexAction() {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->app->entityManager;

        $articles = $em->createQueryBuilder()
            ->select('a')
            ->from(EntityNames::ARTICLE, 'a')
            ->innerJoin('a.page', 'p')
            ->where('p.isStartPage = true')
            ->andWhere('a.isPublished = true')
            ->getQuery()
            ->getResult();

        $this->app->render('index.html.twig', array(
            'articles' => $articles
        ));

    }

    public function pathAction($type, $identifier) {
        switch ($type) {
            case 'articles':
                $this->renderArticle($identifier);
                break;
            case 'pages':
                $this->renderPage($identifier);
                break;
            case 'events':
                $this->renderEvent($identifier);
                break;
            default:
                $this->app->notFound();
        }
    }

    protected function renderArticle($urlName) {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->app->entityManager;

        $article = $em->getRepository(EntityNames::ARTICLE)->findOneByUrlName($urlName);

        if ( ! ($article instanceof Article)) {
            $this->app->notFound();
        }

        $this->checkAccess($article);

        $this->app->render('article.html.twig', array(
            'article' => $article,
            'isPublished' => true
        ));
    }

    protected function renderPage($urlName) {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->app->entityManager;

        $page = $em->getRepository(EntityNames::PAGE)->findOneBy(array(
            'urlName' => $urlName
        ));

        if ( ! ($page instanceof Page)) {
            $this->app->notFound();
        }

        $this->checkAccess($page);

        $this->app->render('page.html.twig', array(
            'page' => $page
        ));
    }

    protected function renderEvent($urlName) {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->app->entityManager;

        $event = $em->getRepository(EntityNames::EVENT)->findOneByUrlName($urlName);

        if ( ! ($event instanceof Event)) {
            $this->app->notFound();
        }

        $this->checkAccess($event);

        $this->app->render('event.html.twig', array(
            'event' => $event
        ));
    }

    protected function checkAccess($obj) {
        if (method_exists($obj, 'getAllowedUserGroups')) {
            /** @var \Doctrine\Common\Collections\ArrayCollection $allowedUserGroups */
            $allowedUserGroups = $obj->getAllowedUserGroups();

            if ($allowedUserGroups->isEmpty()) {
                // we allow access if no restriction is made
                return;
            }

            if (PHP_SESSION_NONE === session_status()) {
                session_start();
            }

            if (isset($_SESSION['user_is_logged_in']) && true === $_SESSION['user_is_logged_in'] && isset($_SESSION['user_id'])) {
                $user = $this->app->entityManager->getRepository(EntityNames::USER)->findOneById($_SESSION['user_id']);

                if ($user instanceof User) {
                    if ($allowedUserGroups->contains($user->getUserGroup())) {
                        return;
                    }
                }
            }
        }

        $this->app->redirect('/login');
    }

}