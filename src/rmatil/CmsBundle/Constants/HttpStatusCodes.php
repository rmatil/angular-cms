<?php

namespace rmatil\CmsBundle\Constants;

class HttpStatusCodes {

    /**
    *  Use 200 - OK if the request was successful and content is returned
    */
    const OK = 200;

    /**
    *  Use 201 - Created if the request was successful and a new resource was created
    *  The URI of the new resource _must_ be in the "Location" header field.
    */
    const CREATED = 201;

    /**
    *  Use 202 - ACCEPTED if the request was successful and no content is returned.
    *  If a resource was/will be modified the URI of the modified resource _must_ be in the "Location" header field.
    */
    const ACCEPTED = 202;

    /**
    *  Use 204 - No Content if the request was successful and no content is returned.
    *  If a resource was/will be modified the URI of the modified resource _must_ be in the "Location" header field.
    */
    const NO_CONTENT = 204;

    /**
    *  Use 304 - Not Modified if the request was successful but did not change since the last request and a cached resource can be used.
    *  The response may contain a ETAG Header.
    */
    const NOT_MODIFIED = 304;

    /**
    *  Use 400 - Bad Request if the request was not successfull because of client error. Such as a missing parameter.
    *  The reponse should contain a explanation of the eror or a link to the explanation of that error.
    */
    const BAD_REQUEST = 400;

    /**
    *  Use 401 - Unautorized if the request access a protected resource but the client is not authenticated.
    *  Note the difference to 403 - FORBIDDEN.
    */
    const UNAUTHORIZED = 401;

    /**
    *  Use 403 - FORBIDDEN if the request access a protected resource, the client is authenticated but does not have th required permissions.
    *  Note the difference to 401 - UNAUTHORIZED.
    */
    const FORBIDDEN = 403;

    /**
     * Use 404 - NOT FOUND if the request tries to access a unexisting resource.
     */
    const NOT_FOUND = 404;

    /**
     * Use 405 - METHOD NOT ALLOWED if the resource exists but is not accessible with the method used.
     * (i.e A POST instead of a GET request)
     */
    const METHOD_NOT_ALLOWED = 405;

    /**
     * Use 409 - CONFLICT if performing the request would cause a conflict with an existing resource, 
     * e.g., creating a resource with an identifier which has been used by other resource and the identifier is expected to be unique.
     */
    const CONFLICT = 409;

    /**
     * Use 500 - INTERNAL SERVER ERROR if the request is valid, but there was an error while processing it.
     * The response may contain content, but be carefull not to leak sensitve information.
     */
    const INTERNAL_SERVER_ERROR = 500;

    /**
     * Use 501 - NOT_IMPLEMENTED if the server does not support the functionality required to fulfill the request.
     * This is the appropriate response when the server does not recognize the request method and 
     * is not capable of supporting it for any resource.
     */
    const NOT_IMPLEMENTED = 501;

}
