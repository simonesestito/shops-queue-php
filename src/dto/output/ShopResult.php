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
 * Class ShopResult
 * It extends the Shop class adding additional information
 * This class should be preferred over Shop, for example, in the user's search results
 */
class ShopResult extends Shop {
    public $distance; // KMs
    public $isFavourite; // The current user included this shop in its favourites

    public function __construct(array $entity) {
        parent::__construct($entity);
        $this->distance = $entity['distance'];
        $this->isFavourite = $entity['isFavourite'] ? true : false;
    }
}