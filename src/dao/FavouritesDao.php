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

class FavouritesDao extends Dao {
    /**
     * Get the favourite shops of the given user
     * @param int $userId
     * @return array Shop records
     */
    public function getFavouritesOfUser(int $userId) {
        return $this->query("SELECT ShopWithCount.*
                                    FROM Favourites 
                                    JOIN ShopWithCount ON Favourites.shopId = ShopWithCount.id
                                    WHERE Favourites.userId = ?", [$userId]);
    }

    /**
     * Add a shop in user's favourites
     * @param int $userId
     * @param int $shopId
     */
    public function addFavourite(int $userId, int $shopId) {
        $this->query("INSERT INTO Favourites (userId, shopId) VALUES (?, ?)", [
            $userId,
            $shopId,
        ]);
    }

    /**
     * Remove a shop from user's favourites
     * @param int $userId
     * @param int $shopId
     */
    public function removeFavourite(int $userId, int $shopId) {
        $this->query("DELETE FROM Favourites WHERE userId = ? AND shopId = ?", [
            $userId,
            $shopId,
        ]);
    }
}