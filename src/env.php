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

/*
 * Environment variables.
 * This file specifically contains dev env variables
 */

$envs = getenv();

define('DB_HOST', $envs['SHOPS_QUEUE_DB_HOST'] ?? 'localhost');
define('DB_USERNAME', $envs['SHOPS_QUEUE_DB_USERNAME'] ?? 'root');
define('DB_PASSWORD', $envs['SHOPS_QUEUE_DB_PASSWORD'] ?? 'root');
define('DB_NAME', $envs['SHOPS_QUEUE_DB_NAME'] ?? 'shops_queue');
define('FCM_SERVER_KEY', $envs['SHOPS_QUEUE_FCM_SERVER_KEY'] ?? '');
define('SENDGRID_API_KEY', $envs['SHOPS_QUEUE_SENDGRID_API_KEY'] ?? '');
define('SENDGRID_TEMPLATE_ID', $envs['SHOPS_QUEUE_SENDGRID_TEMPLATE_ID'] ?? '');
define('SENDGRID_FROM_EMAIL', $envs['SHOPS_QUEUE_SENDGRID_FROM_EMAIL'] ?? '');
define('SKIP_EMAIL_VERIFICATION', $envs['SHOPS_QUEUE_SKIP_EMAIL_VERIFICATION'] ?? 'false');
