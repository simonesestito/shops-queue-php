<?php
require_once __DIR__ . '/utils.php';
requireAll();

// TODO Add auth

// Get path without trailing /
$path = rtrim($_SERVER['PATH_INFO'], '/');

try {
    $controllerClass = findController($path);
    /** @var $controller BaseController */
    $controller = getInstanceOf($controllerClass);
    $subUrl = str_replace($controller::getBaseUrl(), '', $path);
    $result = $controller->handleRequest($subUrl);
    header('Content-Type: application/json');
    echo json_encode($result);
} catch (AppHttpException $e) {
    http_response_code($e->getHttpStatus());
}
