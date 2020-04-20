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
 */
function requireAll($path = __DIR__) {
    global $_onInitCallbacks;
    $scan = glob("$path/*");
    foreach ($scan as $path) {
        if (preg_match('/\.php$/', $path)) {
            /** @noinspection PhpIncludeInspection */
            require_once $path;
        } elseif (is_dir($path)) {
            requireAll($path);
        }
    }

    foreach ($_onInitCallbacks as $callback) {
        // Fire all the callbacks after imports
        call_user_func($callback);
    }
    $_onInitCallbacks = [];
}
