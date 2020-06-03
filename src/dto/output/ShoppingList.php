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

class ShoppingList {
    public $id;
    public $createdAt;
    public $userId;
    public $isReady;
    public $shop;
    public $total;
    public $products;

    /**
     * ShoppingList constructor, given a list of ShoppingListDetails records, with M:N relation
     * It must NOT be empty
     * @param $rawList
     */
    public function __construct($rawList) {
        $this->id = $rawList[0]['shoppingListId'];
        $this->createdAt = strtotime($rawList[0]['createdAt']) * 1000;
        $this->userId = $rawList[0]['userId'];
        $this->isReady = $rawList[0]['isReady'] ? true : false;

        // No field names in conflict
        $this->shop = new Shop($rawList[0]);

        $this->products = array_map(function ($product) {
            // Undo aliases
            $product['id'] = $product['productId'];
            $product['name'] = $product['productName'];
            return new Product($product);
        }, $rawList);

        $this->total = 0.0;
        foreach ($this->products as $product) {
            $this->total += $product->price;
        }
    }
}