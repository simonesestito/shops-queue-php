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

class ShoppingListDao extends Dao {
    /**
     * Get an array of ShoppingListDetail grouped by list ID
     * @param int $userId
     * @return array
     */
    public function getListsByUserId(int $userId) {
        $results = $this->query("SELECT * FROM ShoppingListDetail WHERE userId = ? ORDER BY isReady DESC, id", [$userId]);
        return $this->groupResultByListId($results);
    }

    /**
     * Get an array of ShoppingListDetail grouped by list ID
     * @param int $shopId
     * @return array
     */
    public function getListsByShopId(int $shopId) {
        $results = $this->query("SELECT * FROM ShoppingListDetail WHERE shopId = ? ORDER BY isReady, id", [$shopId]);
        return $this->groupResultByListId($results);
    }

    /**
     * @param int $userId
     * @param NewShoppingList $newShoppingList
     * @return int New list's ID
     */
    public function addUserShoppingList(int $userId, NewShoppingList $newShoppingList): int {
        $listId = $this->query("INSERT INTO ShoppingList (userId) VALUES (?)", [$userId]);

        // Add products
        $insertQuery = '(?,?)';
        $args = [$listId, $newShoppingList->productIds[0]];
        for ($i = 1; $i < count($newShoppingList->productIds); $i++) {
            $insertQuery .= ',(?, ?)';
            array_push($args, $listId, $newShoppingList->productIds[$i]);
        }
        $sql = "INSERT INTO ShoppingList_Products (shoppingListId, productId) VALUES $insertQuery";
        $this->query($sql, $args);

        return $listId;
    }

    /**
     * Delete a shopping list if exists
     * @param int $id
     */
    public function deleteShoppingList(int $id) {
        $this->query("DELETE FROM ShoppingList WHERE id = ?", [$id]);
    }

    /**
     * Set a shopping list as ready
     * @param int $id
     */
    public function prepareShoppingList(int $id) {
        $this->query("UPDATE ShoppingList SET isReady = TRUE WHERE id = ?", [$id]);
    }

    /**
     * Get a list by ID
     * @param int $id
     * @return array
     */
    public function getListById(int $id) {
        return $this->query("SELECT * FROM ShoppingListDetail WHERE shoppingListId = ?", [$id]);
    }

    /**
     * Set a shopping list as retired by the user
     * @param int $id
     */
    public function setListAsRetired(int $id) {
        $this->query("UPDATE ShoppingList SET retireDate = NOW() WHERE retireDate IS NULL AND id = ?", [ $id ]);
    }

    private function groupResultByListId(array $results): array {
        $lists = [];
        foreach ($results as $result) {
            $index = $result['shoppingListId'];
            $list = @$lists[$index] ?? [];
            array_push($list, $result);
            $lists[$index] = $list;
        }
        return array_values($lists);
    }
}
