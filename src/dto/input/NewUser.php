<?php


class NewUser {
    public $name;
    public $surname;
    public $email;
    public $password;

    /**
     * NewUser constructor.
     * The given array will be validated.
     * @param array $rawArray Associative array
     * @throws ModelValidationException
     */
    public function __construct(array $rawArray) {
        /** @var $validator Validator */
        $validator = getInstanceOf(Validator::class);
        $validator->validate([
            'name' => 'is_string',
            'surname' => 'is_string',
            'email' => Validator::isEmailAddress(),
            'password' => 'is_string',
        ], $rawArray);

        $this->name = $rawArray['name'];
        $this->surname = $rawArray['surname'];
        $this->email = strtolower($rawArray['email']);
        $this->password = $rawArray['password'];
    }
}