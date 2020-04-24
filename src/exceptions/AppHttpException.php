<?php

define('HTTP_BAD_REQUEST', 400);
// HTTP Unauthorized: when an auth is not provided
define('HTTP_NOT_LOGGED_IN', 401);
// HTTP Forbidden: when an auth is provided but it isn't sufficient
define('HTTP_NOT_AUTHORIZED', 403);
define('HTTP_NOT_FOUND', 404);
define('HTTP_METHOD_NOT_ALLOWED', 405);
define('HTTP_CONFLICT', 409);
define('HTTP_SERVER_ERROR', 500);

class AppHttpException extends Exception {
    private $httpStatus;

    public function __construct(int $httpStatus, Throwable $previous = null) {
        $message = $previous == null ? '' : $previous->getMessage();
        parent::__construct($message, 0, $previous);
        $this->httpStatus = $httpStatus;
    }

    /**
     * Create an appropriate AppHttpException based on the given exception
     * It also picks the most appropriate HTTP Status Code
     * @param Exception $e
     * @return AppHttpException
     */
    public static function fromException(Exception $e): AppHttpException {
        if ($e instanceof AppHttpException)
            return $e;

        if ($e instanceof ModelValidationException)
            $status = HTTP_BAD_REQUEST;
        elseif ($e instanceof DuplicateEntityException)
            $status = HTTP_CONFLICT;
        elseif ($e instanceof LoginException)
            $status = HTTP_NOT_LOGGED_IN;
        else
            $status = HTTP_SERVER_ERROR;

        return new AppHttpException($status, $e);
    }

    public function getErrorObject(): array {
        return [
            'error' => true,
            'status' => $this->getHttpStatus(),
            'message' => $this->getMessage()
        ];
    }

    public function getHttpStatus(): int {
        return $this->httpStatus;
    }
}
