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


class Product {
    public $id;
    public $name;
    public $ean;
    public $price;

    /**
     * Booking constructor.
     * @param array $rawEntity Record of BookingDetail view
     */
    public function __construct(array $rawEntity) {
        $this->id = $rawEntity['id'];
        $this->name = $rawEntity['name'];
        $this->ean = $rawEntity['ean'];
        $this->price = $rawEntity['price'];
    }
}