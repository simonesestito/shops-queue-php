<?php


class ForeignKeyFailedException extends RuntimeException {
    public function __construct() {
        parent::__construct('Foreign key condition failed');
    }
}