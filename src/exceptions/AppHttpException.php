<?php

define('HTTP_BAD_REQUEST', 400);
// HTTP Unauthorized: when an auth is not provided
define('HTTP_NOT_LOGGED_IN', 401);
// HTTP Forbidden: when an auth is provided but it isn't sufficient
define('HTTP_NOT_AUTHORIZED', 403);
define('HTTP_NOT_FOUND', 404);
define('HTTP_SERVER_ERROR', 500);

class AppHttpException extends Exception {
    private $httpStatus;

    // TODO: Accept an error object to output
    public function __construct(int $httpStatus, Throwable $previous = null) {
        if ($previous == null) {
            parent::__construct('', 0, null);
        } else {
            parent::__construct($previous->getMessage(), $previous->getCode(), $previous);
        }

        $this->httpStatus = $httpStatus;
    }

    public function getHttpStatus(): int {
        return $this->httpStatus;
    }
}