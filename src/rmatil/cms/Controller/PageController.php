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
use rmatil\cms\Exceptions\EntityInvalidException;
use rmatil\cms\Exceptions\EntityNotDeletedException;
use rmatil\cms\Exceptions\EntityNotFoundException;
use rmatil\cms\Exceptions\EntityNotInsertedException;
use rmatil\cms\Exceptions\EntityNotUpdatedException;
use rmatil\cms\Response\ResponseFactory;
use SlimController\SlimController;

/**
 * @package rmatil\cms\Controller
 */
class PageController extends SlimController {

    public function getPagesAction() {
        ResponseFactory::createJsonResponse(
            $this->app,
            $this->app
                ->dataAccessorFactory
                ->getDataAccessor(EntityNames::PAGE)
                ->getAll()
        );
    }

    public function getPageByIdAction($id) {
        try {
            ResponseFactory::createJsonResponse(
                $this->app,
                $this->app
                    ->dataAccessorFactory
                    ->getDataAccessor(EntityNames::PAGE)
                    ->getById($id)
            );
        } catch (EntityNotFoundException $enfe) {
            ResponseFactory::createNotFoundResponse($this->app, $enfe->getMessage());
        }
    }

    public function updatePageAction($pageId) {
        /** @var \rmatil\cms\Entities\Page $page */
        $page = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::PAGE, 'json');
        $page->setId($pageId);
        $page->setAuthor(
            $this->app
                ->entityManager
                ->getRepository(EntityNames::USER)
                ->find($_SESSION['user_id'])
        );

        try {
            $page = $this->app
                ->dataAccessorFactory
                ->getDataAccessor(EntityNames::PAGE)
                ->update($page);
        } catch (EntityInvalidException $eie) {
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::BAD_REQUEST, $eie->getMessage());
            return;
        } catch (EntityNotFoundException $enfe) {
            ResponseFactory::createNotFoundResponse($this->app, $enfe->getMessage());
            return;
        } catch (EntityNotUpdatedException $enue) {
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $enue->getMessage());
            return;
        }

        ResponseFactory::createJsonResponse($this->app, $page);
    }

    public function insertPageAction() {
        /** @var \rmatil\cms\Entities\Page $page */
        $page = $this->app->serializer->deserialize($this->app->request->getBody(), EntityNames::PAGE, 'json');
        $page->setAuthor(
            $this->app
                ->entityManager
                ->getRepository(EntityNames::USER)
                ->find($_SESSION['user_id'])
        );

        try {
            $page = $this->app
                ->dataAccessorFactory
                ->getDataAccessor(EntityNames::PAGE)
                ->insert($page);
        } catch (EntityNotInsertedException $enie) {
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $enie->getMessage());
            return;
        }

        ResponseFactory::createJsonResponseWithCode($this->app, HttpStatusCodes::CREATED, $page);
    }

    public function deletePageByIdAction($id) {
        try {
            $this->app
                ->dataAccessorFactory
                ->getDataAccessor(EntityNames::PAGE)
                ->delete($id);
        } catch (EntityNotFoundException $enfe) {
            ResponseFactory::createNotFoundResponse($this->app, $enfe->getMessage());
            return;
        } catch (EntityNotDeletedException $ende) {
            ResponseFactory::createErrorJsonResponse($this->app, HttpStatusCodes::CONFLICT, $ende->getMessage());
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