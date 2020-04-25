<?php

define('MYSQL_DUPLICATE_ERROR', 1062);
define('MYSQL_FOREIGN_KEY_ERROR', 1452);

onInit(function () {
    /*
     * Connect to the database
     */
    $db = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die();
    provideInstance(mysqli::class, $db);
});