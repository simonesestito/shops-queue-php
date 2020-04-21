<?php

/**
 * Class BaseController
 * It represents the base controller class.
 * Every controller must extend it
 */
abstract class BaseController {
    private $_registeredRoutes = [];

    /**
     * @return string Base URL for the given controller, without parameters
     */
    public abstract static function getBaseUrl(): string;

    /**
     * Handle the request on this controller instance
     * It takes HTTP method and body from superglobal variables
     * @param $subUrl string URL after the controller's base URL
     * @return object Object to return to the client
     * @throws Exception
     */
    public function handleRequest($subUrl) {
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $subUrlSegments = explode('/', $subUrl);
        $subUrlSegmentsCount = count($subUrlSegments);

        foreach ($this->_registeredRoutes as $registeredRoute) {
            if ($registeredRoute['httpMethod'] !== $httpMethod)
                continue;

            $routeSubUrl = $registeredRoute['subUrl'];
            $routeSegments = explode('/', $routeSubUrl);
            if ($subUrlSegmentsCount !== count($routeSegments))
                continue;

            $urlParams = [];
            for ($i = 0; $i < $subUrlSegmentsCount; $i++) {
                $subUrlSegment = $subUrlSegments[$i];
                $routeSegment = $routeSegments[$i];

                if (@$routeSegment[0] === ':') {
                    // This segment is a parameter
                    $urlParams[] = $subUrlSegment;
                } elseif ($routeSegment !== $subUrlSegment) {
                    // Not matched.
                    continue 2;
                }
            }

            // URL matched.
            // Check authentication
            $authRequired = $registeredRoute['authRole'];
            if ($authRequired === '*') {
                // Generic login required
                if (!isset($GLOBALS['auth']))
                    throw new AppHttpException(HTTP_NOT_LOGGED_IN);
            } elseif ($authRequired !== NULL) {
                // Specific login required
                if ($authRequired !== $GLOBALS['auth']['role']) {
                    throw new AppHttpException(HTTP_NOT_AUTHORIZED);
                }
            }

            // Checks passed.
            $class = new ReflectionClass($this);
            $method = $class->getMethod($registeredRoute['methodName']);
            $methodParams = $method->getParameters();
            if (count($methodParams) === count($urlParams) + 1) {
                $body = json_decode(file_get_contents('php://input'), true);
                $modelClass = $methodParams[count($methodParams) - 1]->getClass();
                $model = $modelClass->newInstance($body);
                $urlParams[] = $model;
            }

            $result = $method->invokeArgs($this, $urlParams);
            if ($result == null) {
                // Use empty object instead of null value
                $result = json_decode('{}');
            }
            return $result;
        }

        throw new AppHttpException(HTTP_NOT_FOUND);
    }

    /**
     * Register a controller method to handle a request
     * @param $subUrl string URL after the controller's base URL
     * @param $httpMethod string HTTP method of the request
     * @param $authRole string Auth role required,
     *      or NULL if login isn't required,
     *      or '*' if any role is accepted
     * @param $methodName callable Name of the method to call
     */
    protected function registerRoute($subUrl, $httpMethod, $authRole, $methodName) {
        $this->_registeredRoutes[] = [
            'subUrl' => $subUrl,
            'httpMethod' => $httpMethod,
            'authRole' => $authRole,
            'methodName' => $methodName,
        ];
    }
}

$_controllersMap = [];

/**
 * Register a new controller class.
 * @param $className string Name of controller class
 */
function registerController($className) {
    global $_controllersMap;
    $baseUrl = (new ReflectionClass($className))
        ->newInstanceWithoutConstructor()
        ->getBaseUrl();

    $_controllersMap[] = [
        'className' => $className,
        'baseUrl' => $baseUrl,
    ];
}

/**
 * Find a Controller which matches the given URL
 * @param $url string Current URL
 * @return string Name of controller class, or NULL
 * @throws AppHttpException
 */
function findController($url) {
    global $_controllersMap;

    foreach ($_controllersMap as $controller) {
        if (strpos($url, $controller['baseUrl']) === 0) {
            return $controller['className'];
        }
    }

    throw new AppHttpException(HTTP_NOT_FOUND);
}
