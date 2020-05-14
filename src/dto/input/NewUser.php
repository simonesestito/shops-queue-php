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
 * Class NewUser
 * A user with special capabilities.
 * It can be added by an admin only
 */
class NewUser extends NewSimpleUser {
    public $shopId;
    public $role;
    public $active;

    public function __construct($rawArray) {
        parent::__construct($rawArray);
        /** @var $validator Validator */
        $validator = getInstanceOf(Validator::class);
        $validator->validate([
            // Optional. It's assigned when creating a shop owner account
            'shopId' => Validator::optional('is_int'),
            'role' => Validator::isIn(DB_USER_ROLES),
            'active' => 'is_bool',
        ], $rawArray);

        $this->shopId = $rawArray['shopId'];
        $this->role = is_null($rawArray['role']) ? 'USER' : $rawArray['role'];
        $this->active = $rawArray['active'];
    }
}