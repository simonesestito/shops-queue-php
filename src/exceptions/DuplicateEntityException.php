<?php

class DuplicateEntityException extends RuntimeException {
    public function __construct() {
        parent::__construct('Duplicate value');
    }
}