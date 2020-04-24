<?php
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
} catch (Exception $e) {
    $appException = AppHttpException::fromException($e);
    http_response_code($appException->getHttpStatus());
    echo json_encode($appException->getErrorObject());
}
