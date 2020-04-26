<?php


class NewUser {
    public $name;
    public $surname;
    public $email;
    public $password;
    public $shopId;
    public $role;

    public function __construct($rawArray) {
        /** @var $validator Validator */
        $validator = getInstanceOf(Validator::class);
        $validator->validate([
            'name' => Validator::isString(3),
            'surname' => Validator::isString(3),
            'email' => Validator::filterAs(FILTER_VALIDATE_EMAIL),
            'password' => Validator::isString(8),
            // Optional. It's assigned when creating a shop owner account
            'shopId' => Validator::optional('is_int'),
            // Optional role
            'role' => Validator::optional(Validator::isIn(DB_USER_ROLES)),
        ], $rawArray);

        $this->name = $rawArray['name'];
        $this->surname = $rawArray['surname'];
        $this->email = strtolower($rawArray['email']);
        $this->password = $rawArray['password'];
        $this->shopId = $rawArray['shopId'];
        $this->role = is_null($rawArray['role']) ? 'USER' : $rawArray['role'];
    }
}