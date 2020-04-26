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
}