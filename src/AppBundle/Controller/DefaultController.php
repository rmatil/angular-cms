<?php

namespace AppBundle\Controller;

use rmatil\CmsBundle\Constants\EntityNames;
use rmatil\CmsBundle\Exception\EntityNotFoundException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }

    /**
     * @Route("/test/{id}", name="test_action")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws AccessDeniedHttpException
     */
    public function testAction(Request $request, $id) {
        $responseFactory = $this->get('rmatil_cms.factory.json_response');

        try {
            $article = $this->get('doctrine.orm.default_entity_manager')->getRepository(EntityNames::ARTICLE)->find($id);

            $authorizationChecker = $this->get('security.authorization_checker');

            // check for view access
            if (false === $authorizationChecker->isGranted('VIEW', $article)) {
                // forces to redirect to login form
                throw new AccessDeniedException();
            }

            return $responseFactory->createResponse($article);
        } catch (EntityNotFoundException $ex) {
            return $responseFactory->createNotFoundResponse($ex->getMessage());
        }
    }

    /**
     * @Route("/test2", name="test2_action")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws AccessDeniedHttpException
     */
    public function test2Action(Request $request) {
        echo "<pre>";
        var_dump($this->getParameter('security.role_hierarchy.roles'));
        die;
    }
}
