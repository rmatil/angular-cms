<?php


namespace rmatil\CmsBundle\Response;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use rmatil\CmsBundle\Constants\HttpStatusCodes;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Create JSON responses
 */
class JsonResponseFactory {

    protected $serializer;

    public function __construct(SerializerInterface $serializer) {
        $this->serializer = $serializer;
    }

    /**
     * Returns a JSON response
     *
     * @param $data array|object The object to serialize and add to the response
     *
     * @return JsonResponse The JSON response
     */
    public function createResponse($data) {
        return new JsonResponse(
            $this->serializer->serialize($data, 'json', SerializationContext::create()->enableMaxDepthChecks()),
            HttpStatusCodes::OK,
            ['Content-Type', 'application/json'],
            true
        );
    }


    /**
     * Returns a JSON response with the given status code
     *
     * @param $code integer The HTTP status code
     * @param $data array|object The object to serialize and add to the response
     *
     * @return JsonResponse The JSON response
     */
    public function createResponseWithCode($code, $data) {
        return new JsonResponse(
            $this->serializer->serialize($data, 'json', SerializationContext::create()->enableMaxDepthChecks()),
            $code,
            ['Content-Type', 'application/json'],
            true
        );
    }

    /**
     * Create a JSON not found response
     *
     * @param $message string The not found message
     *
     * @return JsonResponse The not found JSON response
     */
    public function createNotFoundResponse($message) {
        return $this->createErrorResponse(HttpStatusCodes::NOT_FOUND, $message);
    }

    /**
     * Creates a JSON response containing the error
     * and the corresponding error message.
     *
     * @param $code     integer The HTTP status code
     * @param $errorMsg string The error message
     *
     * @return JsonResponse The error JSON response
     */
    public function createErrorResponse($code, $errorMsg) {
        return new JsonResponse(
            [
                'error' => $code,
                'message' => $errorMsg
            ],
            $code,
            ['Content-Type', 'application/json'],
            false
        );
    }

}
