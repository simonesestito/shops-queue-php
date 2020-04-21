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
     * @return User
     */
    public static function fromEntity(array $entity): User {
        $user = new User();
        $user->id = $entity['id'];
        $user->name = $entity['name'];
        $user->surname = $entity['surname'];
        $user->email = $entity['email'];
        $user->role = $entity['roleName'];
        $user->shopId = $entity['shopId'];
        return $user;
    }
}