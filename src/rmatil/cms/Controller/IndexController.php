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

        $this->app->render('index.html.twig', [
            'articles' => $articles
        ]);

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

    /**
     * Renders the root of a particular type,
     * listing all children of its type
     *
     * @param $type string The object type to render
     */
    public function rootAction($type) {
        switch ($type) {
            case 'events':
                $this->renderEvents();
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

        $this->validateAccess($article);

        $twig = clone $this->app->view()->getEnvironment();
        $twig->setLoader(new \Twig_Loader_String());

        $this->app->render('article.html.twig', [
            'twig' => $twig,
            'article' => $article,
            'isPublished' => true
        ]);
    }

    protected function renderPage($urlName) {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->app->entityManager;

        $page = $em->getRepository(EntityNames::PAGE)->findOneBy([
            'urlName' => $urlName
        ]);

        if ( ! ($page instanceof Page)) {
            $this->app->notFound();
        }

        $this->validateAccess($page);

        $twig = clone $this->app->view()->getEnvironment();
        $twig->setLoader(new \Twig_Loader_String());

        $this->app->render('page.html.twig', [
            'twig' => $twig,
            'page' => $page
        ]);
    }

    protected function renderEvent($urlName) {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->app->entityManager;

        $event = $em->getRepository(EntityNames::EVENT)->findOneByUrlName($urlName);

        if ( ! ($event instanceof Event)) {
            $this->app->notFound();
        }

        $this->validateAccess($event);

        $twig = clone $this->app->view()->getEnvironment();
        $twig->setLoader(new \Twig_Loader_String());


        $this->app->render('event.html.twig', [
            'twig' => $twig,
            'event' => $event
        ]);
    }

    /**
     * Render all events accessible by the
     * current logged in user.
     */
    protected function renderEvents() {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->app->entityManager;

        $events = $em->getRepository(EntityNames::EVENT)->findAll();

        $events = array_filter($events, function ($entry) {
            return $this->hasAccess($entry);
        });

        $this->app->render('events.html.twig', [
            'events' => $events
        ]);
    }

    /**
     * Checks the permission of the current session's user
     * for accessing the given object.
     *
     * @see IndexController::validateAccess() for redirection to login page on validation failure
     *
     * @param $obj object The object for which to check the current session's user permission
     *
     * @return bool True, if the user has access, false otherwise
     */
    protected function hasAccess($obj) {
        if (method_exists($obj, 'getAllowedUserGroups')) {
            /** @var \Doctrine\Common\Collections\ArrayCollection $allowedUserGroups */
            $allowedUserGroups = $obj->getAllowedUserGroups();

            if ($allowedUserGroups->isEmpty()) {
                // we allow access if no restriction is made
                return true;
            }

            if (PHP_SESSION_NONE === session_status()) {
                session_start();
            }

            if (isset($_SESSION['user_is_logged_in']) && true === $_SESSION['user_is_logged_in'] && isset($_SESSION['user_id'])) {
                $user = $this->app->entityManager->getRepository(EntityNames::USER)->findOneById($_SESSION['user_id']);

                if ($user instanceof User) {
                    return $allowedUserGroups->contains($user->getUserGroup()) ? true : false;
                }
            }
        }

        return false;
    }

    /**
     * Validates the current session's user permission
     * for accessing the given object.
     * Redirects to the login page, if no access is granted.
     *
     * @param $obj object The object to check access for
     */
    protected function validateAccess($obj) {
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
                    } else {
                        $this->app->flash('login.error', 'You do not have the correct access rights to enter this page');
                        $this->app->redirect('/login');
                    }
                }
            }
        }

        $this->app->redirect('/login?forward=' . $this->app->request->getPath());
    }

}
