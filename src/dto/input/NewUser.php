<?php


class NewUser {
    public $email;
    public $nome;

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
            'email' => Validator::isEmailAddress(),
            'nome' => Validator::optional('is_string'),
        ], $rawArray);

        $this->email = strtolower($rawArray['email']);
        $this->nome = $rawArray['nome'];
    }
}