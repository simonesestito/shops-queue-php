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

require_once __DIR__ . '/BaseController.php';


class AuthController extends BaseController {
    private $userDao;
    private $authService;
    private $emailService;

    public function __construct(UserDao $userDao, AuthService $authService, EmailService $emailService) {
        $this->userDao = $userDao;
        $this->authService = $authService;
        $this->emailService = $emailService;
        $this->registerRoute('/auth/login', 'POST', null, 'login');
        $this->registerRoute('/auth/logout', 'GET', '*', 'logout');
        $this->registerRoute('/auth/signup', 'POST', null, 'signUp');
        $this->registerRoute('/auth/validate/:email/:code', 'GET', null, 'validateEmail');
    }

    public function login(UserLogin $userLogin): AuthResponse {
        return $this->authService->login($userLogin);
    }

    public function logout() {
        // Invalidate current session
        $accessToken = AuthService::getAuthContext()['accessToken'];
        $this->authService->logout($accessToken);
    }

    public function signUp(NewSimpleUser $user) {
        $id = $this->userDao->insertNewSimpleUser($user);

        // Send verification email
        $this->emailService->sendConfirmationToAddress($user->email);
        
        return new UserDetails($this->userDao->getUserById($id));
    }

    public function validateEmail($email, $code) {
        $this->emailService->validateEmailAddress($email, $code);
        // Open the app via a deep link
        header('Location: shopsqueue://login/');
    }
}

onInit(function () {
    registerController(AuthController::class);
});
