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

define('MYSQL_DUPLICATE_ERROR', 1062);
define('MYSQL_FOREIGN_KEY_ERROR', 1452);

const DB_USER_ROLES = [
    'USER',
    'OWNER',
    'ADMIN'
];

onInit(function () {
    /*
     * Connect to the database
     */
    $db = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die();
    provideInstance(mysqli::class, $db);
});