<?php


class ShopDao extends Dao {
    public function getAllShops(): array {
        return $this->query("SELECT * FROM Shop");
    }

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

    public function removeShopById(int $id) {
        $this->query("DELETE FROM Shop WHERE id = ?", [$id]);
    }
}