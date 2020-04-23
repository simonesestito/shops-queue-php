<?php


class UserLogin {
    public $email;
    public $password;

    public function __construct($rawArray) {
        /** @var $validator Validator */
        $validator = getInstanceOf(Validator::class);
        $validator->validate([
            'email' => Validator::filterAs(FILTER_VALIDATE_EMAIL),
            'password' => 'is_string',
        ], $rawArray);

        $this->email = strtolower($rawArray['email']);
        $this->password = $rawArray['password'];
    }
}