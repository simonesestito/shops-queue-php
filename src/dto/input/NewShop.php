<?php


class NewShop {
    public $coordinates;
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
            'coordinates' => Validator::isSchema($validator, [
                'x' => 'is_float',
                'y' => 'is_float',
            ])
        ], $rawArray);

        $this->name = $rawArray['name'];
        $this->address = $rawArray['address'];
        $this->city = $rawArray['city'];
        $this->coordinates = new Coordinates(
            $rawArray['coordinates']['x'],
            $rawArray['coordinates']['y']
        );
    }


    public function toShop(int $id): Shop {
        $shop = new Shop;
        $shop->id = $id;
        $shop->coordinates = $this->coordinates;
        $shop->address = $this->address;
        $shop->name = $this->name;
        $shop->city = $this->city;
        return $shop;
    }
}