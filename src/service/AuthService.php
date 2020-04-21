<?php

define('ACCESS_TOKEN_EXPIRE_HOURS', 24);
define('ACCESS_TOKEN_BITS_LENGTH', 128);
define('REFRESH_TOKEN_EXPIRE_HOURS', 8760); // 1 year
define('REFRESH_TOKEN_BITS_LENGTH', 256);

class AuthService {
    private $userDao;
    private $sessionDao;

    public function __construct(UserDao $userDao, SessionDao $sessionDao) {
        $this->userDao = $userDao;
        $this->sessionDao = $sessionDao;
    }

    /**
     * Login a user
     * @param UserLogin $userLogin User login information
     * @return AuthResponse
     */
    public function login(UserLogin $userLogin): AuthResponse {
        // Check email
        $userEntity = $this->userDao->getUserByEmail($userLogin->email);
        if ($userEntity == null) {
            throw new LoginException('Wrong email');
        }

        // Check password
        if (!password_verify($userLogin->password, $userEntity['password'])) {
            throw new LoginException('Wrong password');
        }

        // Login successful, create a new session
        $newSession = [
            'userId' => $userEntity['id'],
            'accessToken' => $this->generateToken(ACCESS_TOKEN_BITS_LENGTH),
            'accessTokenExpiration' => date('Y-m-d', strtotime('+' . ACCESS_TOKEN_EXPIRE_HOURS . ' hours')),
            'refreshToken' => $this->generateToken(REFRESH_TOKEN_BITS_LENGTH),
            'refreshTokenExpiration' => date('Y-m-d', strtotime('+' . REFRESH_TOKEN_EXPIRE_HOURS . ' hours')),
        ];
        $createdSession = $this->sessionDao->createNewSession($newSession);

        $tokens = UserTokens::fromEntity($createdSession);
        $user = User::fromEntity($userEntity);
        return new AuthResponse($user, $tokens);
    }

    /**
     * Generate a new token.
     * It uses cryptographically secure pseudo-random functions.
     * @param int $bits Number of random bits. It must be a multiple of 8
     * @return string Generated token, encoded with Base64
     */
    private function generateToken(int $bits): string {
        try {
            $bytes = random_bytes($bits / 8);
            return base64_encode($bytes);
        } catch (Exception $e) {
            throw new RuntimeException($e);
        }
    }
}