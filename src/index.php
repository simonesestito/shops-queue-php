<?php
require_once __DIR__ . '/utils.php';
require_all();

// TODO Add auth

// Get path without trailing /
$path = rtrim($_SERVER['PATH_INFO'], '/');

try {
    $controller = instantiateController($path);
    $subUrl = str_replace($controller::getBaseUrl(), '', $path);
    $result = $controller->handleRequest($subUrl);
    header('Content-Type: application/json');
    echo json_encode($result);
} catch (AppHttpException $e) {
    http_response_code($e->getHttpStatus());
}

/**
 * Instantiate a controller which matches the given path
 * @param $path string Path of the request
 * @return BaseController
 * @throws AppHttpException
 */
function instantiateController($path): BaseController {
    $controllerClass = findController($path);
    // TODO Get instance from DI
    return new $controllerClass;
}