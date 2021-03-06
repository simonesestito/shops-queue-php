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
 * Class UserDetails
 * A subclass of User which includes full information about the shop
 */
class UserDetails extends User {
    public $shop;
    public $active;

    /**
     * UserDetails constructor.
     * @param array $entity An array of the UserDetails SQL view records.
     */
    public function __construct(array $entity) {
        parent::__construct($entity);
        $this->active = $entity['active'] ? true : false;

        if ($this->shopId !== null) {
            // Revert SQL aliases
            $shopData = $entity;
            $shopData['name'] = $entity['shopName'];
            $shopData['count'] = $entity['shopBookingsCount'];
            $shopData['id'] = $entity['shopId'];

            $this->shop = new Shop($shopData);
        } else {
            $this->shop = null;
        }
    }
}