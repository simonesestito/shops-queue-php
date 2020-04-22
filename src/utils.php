<?php

$_onInitCallbacks = [];

/**
 * Subscribe to the onInit event.
 * The callback will be called after the current or next call of requireAll().
 * You shouldn't call functions of other imported files without wrapping it in an onInit callback.
 * @param $callback callable Callback
 */
function onInit($callback) {
    global $_onInitCallbacks;
    $_onInitCallbacks[] = $callback;
}

/**
 * Require every file in a directory, recursively.
 * After importing all the files, all callbacks registered with onInit() will be fired.
 * @param $path string Directory to import
 * @param $isRecursive bool Flag to detect if this requireAll() call is recursive
 */
function requireAll(string $path = __DIR__, bool $isRecursive = false) {
    global $_onInitCallbacks;
    $scan = glob("$path/*");
    foreach ($scan as $file) {
        if (preg_match('/\.php$/', $file)) {
            /** @noinspection PhpIncludeInspection */
            require_once $file;
        } elseif (is_dir($file)) {
            requireAll($file, true);
        }
    }

    if (!$isRecursive) {
        foreach ($_onInitCallbacks as $callback) {
            // Fire all the callbacks after imports
            call_user_func($callback);
        }
        $_onInitCallbacks = [];
    }
}

/**
 * Get the Authorization header
 * @return string|null Authorization value
 */
function getAuthorizationHeader() {
    $headers = null;
    if (isset($_SERVER['Authorization'])) {
        // Default
        $headers = trim($_SERVER['Authorization']);
    } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        // Nginx
        $headers = trim($_SERVER['HTTP_AUTHORIZATION']);
    } elseif (function_exists('apache_request_headers')) {
        // Apache
        $requestHeaders = apache_request_headers();
        if (isset($requestHeaders['Authorization'])) {
            $headers = trim($requestHeaders['Authorization']);
        }
    }
    return $headers;
}

/**
 * Get bearer access token from Authorization header
 * @return string|null Bearer token
 */
function getBearerToken() {
    $headers = getAuthorizationHeader();
    if (!empty($headers)) {
        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            return $matches[1];
        }
    }
    return null;
}