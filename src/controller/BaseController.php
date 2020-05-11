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


/**
 * Class BaseController
 * It represents the base controller class.
 * Every controller must extend it
 */
abstract class BaseController {
    private $_registeredRoutes = [];

    /**
     * Register a controller method to handle a request
     * @param $url string URL to match, with parameters
     * @param $httpMethod string HTTP method of the request
     * @param $authRole string Auth role required,
     *      or NULL if login isn't required,
     *      or '*' if any role is accepted
     * @param $methodName callable Name of the method to call
     */
    protected function registerRoute($url, $httpMethod, $authRole, $methodName) {
        $this->_registeredRoutes[] = [
            'url' => $url,
            'httpMethod' => $httpMethod,
            'authRole' => $authRole,
            'methodName' => $methodName,
        ];
    }

    /**
     * Get all the registered routes.
     * @return array Registered routes
     */
    public function getAllRoutes() {
        return $this->_registeredRoutes;
    }
}

$_controllers = [];

/**
 * Register a new controller class.
 * @param $className string Name of controller class
 */
function registerController($className) {
    global $_controllers;
    $_controllers[] = $className;
}

/**
 * Handle the request on this controller instance
 * It takes HTTP method and body from superglobal variables
 * @param $url string URL to match
 * @return object Returned by the handler function
 * @throws Exception
 */
function handleHttpRequest($url) {
    global $_controllers;
    $httpMethod = $_SERVER['REQUEST_METHOD'];
    $urlSegments = explode('/', $url);
    $urlSegmentsCount = count($urlSegments);

    // Collect all routes registered in every controller
    $registeredRoutes = [];
    foreach ($_controllers as $controllerClass) {
        $controller = getInstanceOf($controllerClass);
        $controllerRoutes = $controller->getAllRoutes();

        // Add the controller to the route's info
        $controllerRoutes = array_map(function ($route) use ($controller) {
            $route['controller'] = $controller;
            return $route;
        }, $controllerRoutes);

        array_push($registeredRoutes, ...$controllerRoutes);
    }

    // Flag to indicate if the URL alone has been matched
    $urlMatched = false;

    foreach ($registeredRoutes as $registeredRoute) {
        // Check URL analyzing every segment
        $routeUrl = $registeredRoute['url'];
        $routeSegments = explode('/', $routeUrl);
        if ($urlSegmentsCount !== count($routeSegments))
            continue;

        // Generate URL parameters
        $urlParams = [];
        for ($i = 0; $i < $urlSegmentsCount; $i++) {
            $urlSegment = $urlSegments[$i];
            $routeSegment = $routeSegments[$i];

            if (@$routeSegment[0] === ':') {
                // This segment is a parameter
                $urlParams[] = $urlSegment;
            } elseif ($routeSegment !== $urlSegment) {
                // Not matched.
                continue 2;
            }
        }

        // URL matched.
        $urlMatched = true;

        // Check HTTP method
        if ($registeredRoute['httpMethod'] !== $httpMethod)
            continue;

        // Check authentication
        $authRequired = $registeredRoute['authRole'];
        if ($authRequired === '*') {
            // Generic login required
            if (AuthService::getAuthContext() === null)
                throw new AppHttpException(HTTP_NOT_LOGGED_IN);
        } elseif ($authRequired !== NULL) {
            // Specific login required
            $userRole = AuthService::getAuthContext()['role'];
            if ($userRole == null) {
                throw new AppHttpException(HTTP_NOT_LOGGED_IN);
            } elseif ($authRequired !== $userRole) {
                $errorMessage = "Required role: $authRequired, detected role: $userRole";
                throw new AppHttpException(HTTP_NOT_AUTHORIZED, $errorMessage);
            }
        }

        // Checks passed.
        // Invoke the controller function
        $controller = $registeredRoute['controller'];
        $class = new ReflectionClass($controller);
        $method = $class->getMethod($registeredRoute['methodName']);
        $methodParams = $method->getParameters();

        // Convert types if required
        for ($i = 0; $i < count($methodParams); $i++) {
            $methodParam = $methodParams[$i];
            if ($methodParam->getType() === null)
                continue;

            $methodParamType = $methodParam->getType()->__toString();
            if ($methodParamType === 'int')
                $urlParams[$i] = intval($urlParams[$i]);
        }

        // Add the input body (e.g.: in HTTP POST requests)
        // to the method parameters array
        if (count($methodParams) === count($urlParams) + 1) {
            $body = json_decode(file_get_contents('php://input'), true);
            $modelClass = $methodParams[count($methodParams) - 1]->getClass();
            $model = $modelClass->newInstance($body);
            $urlParams[] = $model;
        }

        return $method->invokeArgs($controller, $urlParams);
    }

    if ($urlMatched) {
        // URL has been matched
        // but with a different HTTP method
        throw new AppHttpException(HTTP_METHOD_NOT_ALLOWED);
    } else {
        // URL wasn't matched by any handler function
        throw new AppHttpException(HTTP_NOT_FOUND);
    }
}


