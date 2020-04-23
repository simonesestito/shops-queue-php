<?php


class ShopDao extends Dao {
    /**
     * Create a new shop
     * @param NewShop $newShop
     * @return int New record's ID
     */
    public function insertNewShop(NewShop $newShop): int {
        return $this->query("INSERT INTO Shop (latitude, longitude, address, name, city) VALUES (?, ?, ?, ?, ?)", [
            $newShop->latitude,
            $newShop->longitude,
            $newShop->address,
            $newShop->name,
            $newShop->city
        ]);
    }

    /**
     * Remove an existing shop from database
     * @param int $id Shop ID
     */
    public function removeShopById(int $id) {
        $this->query("DELETE FROM Shop WHERE id = ?", [$id]);
    }

    /**
     * Find shops near the user's location, ordered by ascending distance
     *
     * Distance in KM is calculated according to:
     * @link https://developers.google.com/maps/solutions/store-locator/clothing-store-locator#findnearsql
     *
     * @param float $fromLat the user's latitude
     * @param float $fromLon the user's longitude
     * @param int $offset Number of items to skip
     * @param int $limit Max number of items to return
     * @param string $query Filter by name
     * @return array Array of shops, with a 'distance' field in KMs
     */
    public function findShops(float $fromLat, float $fromLon, int $offset, int $limit, string $query): array {
        $sql = "
        SELECT *, DISTANCE_KM(?, ?, longitude, latitude) AS distance
        FROM Shop
        WHERE name LIKE ?
        ORDER BY distance
        LIMIT ?, ?
        ";

        return $this->query($sql, [$fromLon, $fromLat, "%$query%", $offset, $limit]);
    }
}