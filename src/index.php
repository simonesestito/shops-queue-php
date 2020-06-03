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

require_once __DIR__ . '/utils.php';
requireAll();

// Login the user
$token = getBearerToken();
/** @var AuthService $authService */
$authService = getInstanceOf(AuthService::class);
$authContext = $authService->createAuthContext($token);
AuthService::setAuthContext($authContext);

// Get path without trailing /
$path = isset($_SERVER['PATH_INFO']) ? rtrim($_SERVER['PATH_INFO'], '/') : '';

header('Content-Type: application/json');

try {
    $result = handleHttpRequest($path);
    echo json_encode($result);
} catch (Throwable $e) {
    $appException = AppHttpException::fromException($e);
    http_response_code($appException->getHttpStatus());
    echo json_encode($appException->getErrorObject());
}
