<?php

class ModelValidationException extends Exception {
    private $field;

    public function __construct(string $field) {
        parent::__construct("Error validating field $field");
        $this->field = $field;
    }

    /**
     * Get the field which caused this exception to be thrown
     * @return string Target field
     */
    public function getField(): string {
        return $this->field;
    }
}
