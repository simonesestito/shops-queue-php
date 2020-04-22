<?php

/**
 * Response of a login request
 */
class AuthResponse {
    public $user;
    public $accessToken;

    public function __construct(User $user, string $accessToken) {
        $this->user = $user;
        $this->accessToken = $accessToken;
    }
}