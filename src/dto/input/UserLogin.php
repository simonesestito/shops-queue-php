<?php


class UserLogin {
    public $email;
    public $password;

    public function __construct($rawArray) {
        /** @var $validator Validator */
        $validator = getInstanceOf(Validator::class);
        $validator->validate([
            'email' => Validator::isEmailAddress(),
            'password' => 'is_string',
        ], $rawArray);

        $this->email = strtolower($rawArray['email']);
        $this->password = $rawArray['password'];
    }
}