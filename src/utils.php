<?php

function require_all($path = __DIR__) {
    $scan = glob("$path/*");
    foreach ($scan as $path) {
        if (preg_match('/\.php$/', $path)) {
            require_once $path;
        } elseif (is_dir($path)) {
            require_all($path);
        }
    }
}