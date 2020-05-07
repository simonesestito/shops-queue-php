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

class ShopDao extends Dao {
    /**
     * Create a new shop
     * @param NewShop $newShop
     * @return int New record's ID
     */
    public function insertNewShop(NewShop $newShop): int {
        return $this->query("INSERT INTO Shop (latitude, longitude, address, name) VALUES (?, ?, ?, ?)", [
            $newShop->latitude,
            $newShop->longitude,
            $newShop->address,
            $newShop->name
        ]);
    }

    /**
     * Update a shop
     * @param int $id
     * @param NewShop $newShop
     */
    public function updateShop(int $id, NewShop $newShop) {
        $sql = "UPDATE Shop
                SET latitude = ?,
                longitude = ?,
                address = ?,
                name = ?
                WHERE id = ?";
        $this->query($sql, [
            $newShop->latitude,
            $newShop->longitude,
            $newShop->address,
            $newShop->name,
            $id
        ]);
    }

    /**
     * List all the shops, without distance
     * It uses pagination
     * @param int $offset
     * @param int $limit
     * @param string $query
     * @return array Associative array. Key 'count' has the total rows count, 'data' has the actual result
     */
    public function listShops(int $offset, int $limit, string $query = '') {
        $data = $this->query("SELECT SQL_CALC_FOUND_ROWS * FROM ShopWithCount WHERE name LIKE ? ORDER BY name LIMIT ?, ?",
            ["%$query%", $offset, $limit]);
        $count = $this->query("SELECT FOUND_ROWS() AS c")[0]['c'];

        return [
            'data' => $data,
            'count' => $count,
        ];
    }

    /**
     * Get an existing shop by ID
     * @param int $id
     * @return array|null ShopWithCount record
     */
    public function getShopById(int $id) {
        $result = $this->query("SELECT * FROM ShopWithCount WHERE id = ?", [$id]);
        return @$result[0];
    }

    /**
     * Remove an existing shop from database
     * @param int $id Shop ID
     */
    public function removeShopById(int $id) {
        $this->query("DELETE FROM Shop WHERE id = ?", [$id]);
    }

    /**
     * Find shops near the user's location, ordered by ascending distance.
     * Distance in KM is calculated using user-defined DISTANCE_KM function.
     * It returns both the data found and the total number of items available.
     * @link https://dev.mysql.com/doc/refman/8.0/en/information-functions.html#function_found-rows
     *
     * @param float $fromLat the user's latitude
     * @param float $fromLon the user's longitude
     * @param int $offset Number of items to skip
     * @param int $limit Max number of items to return
     * @param string $query Filter by name
     * @return array Associative array. Key 'count' has the total rows count, 'data' has the actual result
     */
    public function findShops(float $fromLat, float $fromLon, int $offset, int $limit, string $query = ''): array {
        $sql = "
        SELECT SQL_CALC_FOUND_ROWS *, DISTANCE_KM(?, ?, longitude, latitude) AS distance
        FROM ShopWithCount
        WHERE name LIKE ?
        ORDER BY distance
        LIMIT ?, ?
        ";

        $data = $this->query($sql, [$fromLon, $fromLat, "%$query%", $offset, $limit]);
        $count = $this->query("SELECT FOUND_ROWS() AS c")[0]['c'];

        return [
            'data' => $data,
            'count' => $count,
        ];
    }
}