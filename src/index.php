<?php
require_once __DIR__ . '/utils.php';
requireAll();

// TODO Add auth

// Get path without trailing /
$path = isset($_SERVER['PATH_INFO']) ? rtrim($_SERVER['PATH_INFO'], '/') : '';

header('Content-Type: application/json');

try {
    $controllerClass = findController($path);
    /** @var $controller BaseController */
    $controller = getInstanceOf($controllerClass);

    $subUrl = str_replace($controller::getBaseUrl(), '', $path);

    $result = $controller->handleRequest($subUrl);
    echo json_encode($result);
} catch (Exception $e) {
    $appException = AppHttpException::fromException($e);
    http_response_code($appException->getHttpStatus());
    echo json_encode($appException->getErrorObject());
}
