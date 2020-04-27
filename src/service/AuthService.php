<?php
/**
 * Copyright 2020 Simone Sestito
 * This file is part of Shops Queue.
 *
 * Shops Queue is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Shops Queue is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with Shops Queue.  If not, see <http://www.gnu.org/licenses/>.
 */

define('ACCESS_TOKEN_BITS_LENGTH', 256);

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
        ];
        $createdSessionId = $this->sessionDao->createNewSession($newSession);
        $createdSession = $this->sessionDao->getSessionById($createdSessionId);

        $user = new User($userEntity);
        return new AuthResponse($user, $createdSession['accessToken']);
    }

    /**
     * Create an auth context based on the given access token
     * It should be assigned to <code>$GLOBAL['auth'];</code>
     * @param string|null $accessToken
     * @return array|null Auth context
     */
    public function createAuthContext($accessToken) {
        if (is_null($accessToken)) {
            return null;
        }

        // Validate access token
        $sessionInfo = $this->sessionDao->getSessionByAccessToken($accessToken);
        if ($sessionInfo == null) {
            // Invalid access token
            return null;
        }

        return $sessionInfo;
    }

    /**
     * Invalidate the given session with both access and refresh tokens.
     * It doesn't throw if the session doesn't exist.
     * @param $accessToken string Access token for the session to invalidate
     */
    public function logout(string $accessToken) {
        $this->sessionDao->removeSessionByAccessToken($accessToken);
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

    /**
     * @return mixed Record of SessionDetail SQL view
     */
    public static final function getAuthContext() {
        return @$GLOBALS['auth'];
    }

    public static final function setAuthContext($authContext) {
        $GLOBALS['auth'] = $authContext;
    }
}