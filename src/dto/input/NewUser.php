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

class NewUser {
    public $name;
    public $surname;
    public $email;
    public $password;
    public $shopId;
    public $role;

    public function __construct($rawArray) {
        /** @var $validator Validator */
        $validator = getInstanceOf(Validator::class);
        $validator->validate([
            'name' => Validator::isString(3),
            'surname' => Validator::isString(3),
            'email' => Validator::filterAs(FILTER_VALIDATE_EMAIL),
            'password' => Validator::isString(8),
            // Optional. It's assigned when creating a shop owner account
            'shopId' => Validator::optional('is_int'),
            // Optional role
            'role' => Validator::optional(Validator::isIn(DB_USER_ROLES)),
        ], $rawArray);

        $this->name = $rawArray['name'];
        $this->surname = $rawArray['surname'];
        $this->email = strtolower($rawArray['email']);
        $this->password = $rawArray['password'];
        $this->shopId = $rawArray['shopId'];
        $this->role = is_null($rawArray['role']) ? 'USER' : $rawArray['role'];
    }
}