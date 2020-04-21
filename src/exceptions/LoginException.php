<?php


class LoginException extends RuntimeException {
    public function __construct($message = "") {
        parent::__construct($message);
    }
}