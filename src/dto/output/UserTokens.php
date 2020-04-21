<?php


class UserTokens {
    public $accessToken;
    public $accessTokenExpiresIn;
    public $refreshToken;
    public $refreshTokenExpiresIn;

    /**
     * Create an instance of UserTokens from a DB result
     * @param array $entity DB associative array
     * @return UserTokens
     */
    public static function fromEntity(array $entity): UserTokens {
        try {
            $now = new DateTime();
            $accessTokenExpiration = new DateTime($entity['accessTokenExpiration']);
            $refreshTokenExpiration = new DateTime($entity['refreshTokenExpiration']);
        } catch (Exception $e) {
            throw new RuntimeException('Error parsing dates', $e);
        }

        $tokens = new UserTokens();
        $tokens->accessToken = $entity['accessToken'];
        $tokens->refreshToken = $entity['refreshToken'];
        $tokens->accessTokenExpiresIn = $now->diff($accessTokenExpiration)->s;
        $tokens->refreshTokenExpiresIn = $now->diff($refreshTokenExpiration)->s;
        return $tokens;
    }
}