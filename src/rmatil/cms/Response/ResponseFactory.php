<?php


namespace rmatil\cms\Response;

use rmatil\cms\Constants\HttpStatusCodes;

/**
 * Creates responses for the slim application
 *
 * @package rmatil\cms\Response
 */
class ResponseFactory {

    /**
     * Appends the given data to the app instance.
     * Additionally, sets the expiration header to 0, the
     * Content-Type to application/json and finally the HTTP status code to 200 OK
     *
     * @param $app \Slim\Slim The slim application instance
     * @param $data mixed The data to append as body to the response
     */
    public static function createJsonResponse($app, $data) {
        $app->expires(0);
        $app->response->header('Content-Type', 'application/json');
        $app->response->setStatus(HttpStatusCodes::OK);
        $app->response->setBody($app->serializer->serialize($data, 'json'));
    }

    /**
     * Appends the given data to the app instance.
     * Additionally, sets the expiration header to 0, the
     * Content-Type to application/json and finally the HTTP status code to the
     * submitted one
     *
     * @param $app \Slim\Slim The slim application instance
     * @param $code integer The HTTP status code
     * @param $data mixed THe data to append as body to the response
     */
    public static function createJsonResponseWithCode($app, $code, $data) {
        $app->expires(0);
        $app->response->header('Content-Type', 'application/json');
        $app->response->setStatus($code);
        $app->response->setBody($app->serializer->serialize($data, 'json'));
    }

    /**
     * @param $app \Slim\Slim The slim application instance
     */
    public static function createNotFoundResponse($app) {
        $app->response->setStatus(HttpStatusCodes::NOT_FOUND);
    }

    /**
     * Creates a Basic Authorization Response which prompts a
     * login maks to the Client
     *
     * @param $app \Slim\Slim The slim application instance
     * @param $realm string The realm of the request
     */
    public static function createUnauthorizedResponse($app, $realm) {
        $app->response->status(HttpStatusCodes::UNAUTHORIZED);
        $app->response->header('WWW-Authenticate', sprintf('Basic realm="%s"', $realm));
    }

    /**
     * Creates a JSON response containing the error and the corresponding
     * error message.
     *
     * @param $app \Slim\Slim The slim application instance
     * @param $code integer The HTTP status code
     * @param $errorMsg string The error message
     */
    public static function createErrorJsonResponse($app, $code, $errorMsg) {
        $app->response->header('Content-Type', 'application/json');
        $app->response->status($code);
        $app->response->body(json_encode(array(
            'error' => $code,
            'message' => $errorMsg
        )));
    }

}