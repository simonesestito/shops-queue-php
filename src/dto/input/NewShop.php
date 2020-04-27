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

class NewShop {
    public $latitude;
    public $longitude;
    public $address;
    public $name;
    public $city;

    public function __construct($rawArray) {
        /** @var $validator Validator */
        $validator = getInstanceOf(Validator::class);
        $validator->validate([
            'address' => Validator::isString(3),
            'name' => Validator::isString(3),
            'city' => Validator::isString(3),
            'latitude' => 'is_float',
            'longitude' => 'is_float',
        ], $rawArray);

        $this->name = $rawArray['name'];
        $this->address = $rawArray['address'];
        $this->city = $rawArray['city'];
        $this->latitude = $rawArray['latitude'];
        $this->longitude = $rawArray['longitude'];
    }
}