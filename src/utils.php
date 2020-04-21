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
