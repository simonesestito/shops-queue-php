<?php


class Shop {
    public $id;
    public $longitude;
    public $latitude;
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
        $shop->latitude = $entity['latitude'];
        $shop->longitude = $entity['longitude'];
        $shop->address = $entity['address'];
        $shop->name = $entity['name'];
        $shop->city = $entity['city'];
        return $shop;
    }
}