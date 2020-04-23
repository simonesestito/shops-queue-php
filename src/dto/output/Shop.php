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
     */
    public function __construct(array $entity) {
        $this->id = $entity['id'];
        $this->latitude = $entity['latitude'];
        $this->longitude = $entity['longitude'];
        $this->address = $entity['address'];
        $this->name = $entity['name'];
        $this->city = $entity['city'];
    }
}