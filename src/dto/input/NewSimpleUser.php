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

class NewSimpleUser {
    public $name;
    public $surname;
    public $email;
    public $password;

    public function __construct($rawArray) {
        /** @var $validator Validator */
        $validator = getInstanceOf(Validator::class);
        $validator->validate([
            'name' => Validator::isString(3),
            'surname' => Validator::isString(3),
            'email' => Validator::filterAs(FILTER_VALIDATE_EMAIL),
            'password' => Validator::isString(8),
        ], $rawArray);

        $this->name = $rawArray['name'];
        $this->surname = $rawArray['surname'];
        $this->email = $rawArray['email'];
        $this->password = $rawArray['password'];
    }
}