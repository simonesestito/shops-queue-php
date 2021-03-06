<?php
/**
 * Copyright 2020 Simone Sestito
 * This file is part of Shops Queue.
 *
 * Shops Queue is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Shops Queue is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with Shops Queue.  If not, see <http://www.gnu.org/licenses/>.
 */

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

    /**
     * AppHttpException constructor.
     * @param int $httpStatus
     * @param string|Throwable|null $previous Causing exception or motivation
     */
    public function __construct(int $httpStatus, $previous = null) {
        if ($previous === null)
            $message = '';
        elseif (is_string($previous))
            $message = $previous;
        elseif ($previous instanceof Throwable)
            $message = $previous->getMessage();
        else
            $message = '';

        parent::__construct($message);
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
        elseif ($e instanceof ForeignKeyFailedException)
            $status = HTTP_NOT_FOUND;
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
