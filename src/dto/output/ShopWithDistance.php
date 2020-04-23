<?php


class ShopWithDistance extends Shop {
    public $distance; // KMs

    public function __construct(array $entity) {
        parent::__construct($entity);
        $this->distance = $entity['distance'];
    }
}