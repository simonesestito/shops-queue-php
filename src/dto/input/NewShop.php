<?php


class NewShop {
    public $latitude;
    public $longitude;
    public $address;
    public $name;
    public $city;

    public function __construct($rawArray) {
        /** @var $validator Validator */
        $validator = getInstanceOf(Validator::class);
        $validator->validate([
            'address' => Validator::isString(3),
            'name' => Validator::isString(3),
            'city' => Validator::isString(3),
            'latitude' => 'is_float',
            'longitude' => 'is_float',
        ], $rawArray);

        $this->name = $rawArray['name'];
        $this->address = $rawArray['address'];
        $this->city = $rawArray['city'];
        $this->latitude = $rawArray['latitude'];
        $this->longitude = $rawArray['longitude'];
    }


    public function toShop(int $id): Shop {
        $shop = new Shop;
        $shop->id = $id;
        $shop->longitude = $this->longitude;
        $shop->latitude = $this->latitude;
        $shop->address = $this->address;
        $shop->name = $this->name;
        $shop->city = $this->city;
        return $shop;
    }
}