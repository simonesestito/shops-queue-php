<?php


class NewShopAccount {
    public $newShop;
    public $newUser;

    public function __construct($rawArray) {
        $this->newShop = new NewShop($rawArray['shop']);
        $this->newUser = new NewUser($rawArray['user']);
    }
}