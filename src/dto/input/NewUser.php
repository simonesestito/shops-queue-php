<?php


class NewUser {
    public $name;
    public $surname;
    public $email;
    public $password;

    public function __construct($rawArray) {
        /** @var $validator Validator */
        $validator = getInstanceOf(Validator::class);
        $validator->validate([
            'name' => Validator::isString(3),
            'surname' => Validator::isString(3),
            'email' => Validator::filterAs(FILTER_VALIDATE_EMAIL),
            'password' => Validator::isString(8),
        ], $rawArray);

        $this->name = $rawArray['name'];
        $this->surname = $rawArray['surname'];
        $this->email = strtolower($rawArray['email']);
        $this->password = $rawArray['password'];
    }
}