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

class Shop {
    public $id;
    public $longitude;
    public $latitude;
    public $address;
    public $name;
    public $city;
    public $count;

    /**
     * Create an instance of ShopWithCount from a DB result
     * @param array $entity DB associative array
     */
    public function __construct(array $entity) {
        $this->id = $entity['id'];
        $this->latitude = $entity['latitude'];
        $this->longitude = $entity['longitude'];
        $this->address = $entity['address'];
        $this->name = $entity['name'];
        $this->city = $entity['city'];
        $this->count = $entity['count'];
    }
}