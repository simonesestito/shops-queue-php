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

class ProductDao extends Dao {
    /**
     * Get all the products offered by a shop
     * @param int $shopId
     */
    public function getProductsByShopId(int $shopId) {
        // TODO
    }

    /**
     * Add a new product to a shop
     * @param int $shopId
     * @param NewProduct $newProduct
     * @return int New product's ID
     */
    public function addProduct(int $shopId, NewProduct $newProduct): int {
        // TODO
    }

    /**
     * Edit an existing product.
     * User authorization checks must be performed in the Controller layer
     * @param int $productId
     * @param NewProduct $newProduct
     */
    public function editProduct(int $productId, NewProduct $newProduct) {
        // TODO
    }

    /**
     * Delete a product by ID, if it exists
     * User authorization checks must be performed in the Controller layer
     * @param int $productId
     */
    public function deleteProduct(int $productId) {
        // TODO
    }
}