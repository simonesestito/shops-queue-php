<?php

class ModelValidationException extends RuntimeException {
    public function __construct(string $field) {
        parent::__construct("Error validating field $field");
    }
}
