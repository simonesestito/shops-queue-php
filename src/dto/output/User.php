<?php


class User {
    public $id;
    public $name;
    public $surname;
    public $email;
    public $role;
    public $shopId;

    /**
     * Create an instance of User from a DB result
     * @param array $entity DB associative array
     */
    public function __construct(array $entity) {
        $this->id = $entity['id'];
        $this->name = $entity['name'];
        $this->surname = $entity['surname'];
        $this->email = $entity['email'];
        $this->role = $entity['roleName'];
        $this->shopId = $entity['shopId'];
    }
}