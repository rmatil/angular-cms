<?php


namespace rmatil\CmsBundle\Controller;


use rmatil\CmsBundle\Constants\HttpStatusCodes;
use rmatil\CmsBundle\Exception\EntityInvalidException;
use rmatil\CmsBundle\Exception\EntityNotFoundException;
use rmatil\CmsBundle\Exception\EntityNotInsertedException;
use rmatil\CmsBundle\Exception\EntityNotUpdatedException;
use rmatil\CmsBundle\Constants\EntityNames;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ArticleCategoryController extends Controller {

    /**
     * @return JsonResponse
     *
     * @Route("/article-categories", name="get_article_categories", methods={"GET"})
     */
    public function getArticleCategoriesAction() {
        $responseFactory = $this->get('rmatil_cms.factory.json_response');
        $categories = $this->get('rmatil_cms.data_accessor.article_category')->getAll();

        return $responseFactory->createResponse($categories);
    }

    /**
     * @param $id
     *
     * @return JsonResponse
     *
     * @Route("/article-categories/{id}", name="get_article_category", methods={"GET"})
     */
    public function getArticleCategoryByIdAction($id) {
        $responseFactory = $this->get('rmatil_cms.factory.json_response');

        try {
            $category = $this->get('rmatil_cms.data_accessor.article_category')->getById($id);

            return $responseFactory->createResponse($category);
        } catch (EntityNotFoundException $ex) {
            return $responseFactory->createNotFoundResponse($ex->getMessage());
        }
    }

    /**
     * @param         $id
     * @param Request $request
     *
     * @return JsonResponse
     * 
     * @Route("/article-categories/{id}", name="update_article_category", methods={"PUT"})
     */
    public function updateArticleCategoryAction($id, Request $request) {
        $responseFactory = $this->get('rmatil_cms.factory.json_response');

        /** @var \rmatil\CmsBundle\Entity\ArticleCategory $articleCategory */
        $articleCategory = $this->get('jms_serializer')->deserialize(
            $request->getContent(),
            EntityNames::ARTICLE_CATEGORY,
            'json'
        );

        $articleCategory->setId($id);

        try {

            $obj = $this->get('rmatil_cms.data_accessor.article_category')->update($articleCategory);

            return $responseFactory->createResponse($obj);

        } catch (EntityInvalidException $eie) {
            return $responseFactory->createErrorResponse(HttpStatusCodes::BAD_REQUEST, $eie->getMessage());
        } catch (EntityNotFoundException $enfe) {
            return $responseFactory->createNotFoundResponse($enfe->getMessage());
        } catch (EntityNotUpdatedException $enue) {
            return $responseFactory->createErrorResponse(HttpStatusCodes::CONFLICT, $enue->getMessage());
        }
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @Route("/article-categories", name="insert_article_category", methods={"POST"})
     */
    public function insertArticleCategoryAction(Request $request) {
        $responseFactory = $this->get('rmatil_cms.factory.json_response');

        /** @var \rmatil\CmsBundle\Entity\ArticleCategory $articleCategory */
        $articleCategory = $this->get('jms_serializer')->deserialize(
            $request->getContent(),
            EntityNames::ARTICLE_CATEGORY,
            'json'
        );

        try {

            $obj = $this->get('rmatil_cms.data_accessor.article_category')->insert($articleCategory);

            return $responseFactory->createResponseWithCode(HttpStatusCodes::CREATED, $obj);

        } catch (EntityNotInsertedException $enie) {
            return $responseFactory->createErrorResponse(HttpStatusCodes::CONFLICT, $enie->getMessage());
        }
    }

    /**
     * @param $id
     *
     * @return JsonResponse
     *
     * @Route("/article-categories/{id}", name="delete_article_category", methods={"DELETE"})
     */
    public function deleteArticleCategoryByIdAction($id) {
        $responseFactory = $this->get('rmatil_cms.factory.json_response');

        try {

            $this->get('rmatil_cms.data_accessor.article_category')->delete($id);

        } catch (EntityNotFoundException $enfe) {
            return $responseFactory->createNotFoundResponse($enfe->getMessage());
        }

        return $responseFactory->createResponseWithCode(HttpStatusCodes::NO_CONTENT, "");
    }

}
