<?php


class FavouritesDao extends Dao {
    /**
     * Get the favourite shops of the given user
     * @param int $userId
     * @return array Shop records
     */
    public function getFavouritesOfUser(int $userId) {
        return $this->query("SELECT Shop.* FROM Favourites 
                                    JOIN Shop ON Favourites.shopId = Shop.id
                                    WHERE Favourites.userId = ?", [$userId]);
    }
}