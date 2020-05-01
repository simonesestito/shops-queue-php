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

class Booking {
    public $bookingId;
    public $createdAt;
    public $user;
    public $shop;

    /**
     * Booking constructor.
     * @param array $rawEntity Record of BookingDetail view
     */
    public function __construct(array $rawEntity) {
        $this->bookingId = $rawEntity['bookingId'];
        $this->createdAt = strtotime($rawEntity['createdAt']) * 1000;

        // Undo aliases
        $rawEntity['id'] = $rawEntity['userId'];
        $this->user = new User($rawEntity);

        $rawEntity['id'] = $rawEntity['bookingShopId'];
        $rawEntity['name'] = $rawEntity['shopName'];
        $this->shop = new Shop($rawEntity);
    }
}