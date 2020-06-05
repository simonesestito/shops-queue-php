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
     * @return array
     */
    public function getProductsByShopId(int $shopId): array {
        return $this->query("SELECT * FROM Product WHERE shopId = ?", [$shopId]);
    }

    /**
     * Get a product by ID
     * @param int $productId
     * @return array|null
     */
    public function getProductById(int $productId) {
        $results = $this->query("SELECT * FROM Product WHERE id = ?", [$productId]);
        return @$results[0];
    }

    /**
     * Add a new product to a shop
     * @param int $shopId
     * @param NewProduct $newProduct
     * @return int New product's ID
     */
    public function addProduct(int $shopId, NewProduct $newProduct): int {
        return $this->query("INSERT INTO Product (name, ean, price, shopId)
                                    VALUES (?, ?, ?, ?)", [
            $newProduct->name,
            $newProduct->ean,
            $newProduct->price,
            $shopId
        ]);
    }

    /**
     * Edit an existing product.
     * @param int $productId
     * @param int $shopId
     * @param NewProduct $newProduct
     */
    public function editProduct(int $productId, int $shopId, NewProduct $newProduct) {
        $this->query("UPDATE Product SET
        name = ?,
        ean = ?,
        price = ?
        WHERE id = ? AND shopId = ?", [
            $newProduct->name,
            $newProduct->ean,
            $newProduct->price,
            $productId,
            $shopId,
        ]);
    }

    /**
     * Delete a product by ID, if it exists
     * @param int $productId
     * @param int $shopId
     */
    public function deleteProduct(int $productId, int $shopId) {
        $this->query("DELETE FROM Product WHERE id = ? AND shopId = ?", [
            $productId,
            $shopId
        ]);
    }

    /**
     * Get all the products based on the given IDs
     * @param array $ids
     * @return array
     */
    public function getProductsByIds(array $ids): array {
        $arrayTemplate = arraySqlArg(count($ids));
        return $this->query("SELECT * FROM Product WHERE id IN $arrayTemplate", $ids);
    }
}