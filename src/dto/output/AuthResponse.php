<?php

/**
 * Response of a login request
 */
class AuthResponse {
    public $user;
    public $tokens;

    public function __construct(User $user, UserTokens $userTokens) {
        $this->user = $user;
        $this->tokens = $userTokens;
    }
}