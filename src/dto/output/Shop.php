<?php


class Shop {
    public $id;
    public $coordinates;
    public $address;
    public $name;
    public $city;

    /**
     * Create an instance of Shop from a DB result
     * @param array $entity DB associative array
     * @return Shop
     */
    public static function fromEntity(array $entity): Shop {
        $shop = new Shop;
        $shop->id = $entity['id'];
        $shop->coordinates = new Coordinates($entity['xCoordinate'], $entity['yCoordinate']);
        $shop->address = $entity['address'];
        $shop->name = $entity['name'];
        $shop->city = $entity['city'];
        return $shop;
    }
}