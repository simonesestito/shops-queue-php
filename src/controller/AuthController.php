<?php

require_once __DIR__ . '/BaseController.php';


class AuthController extends BaseController {
    private $userDao;
    private $authService;

    public function __construct(UserDao $userDao, AuthService $authService) {
        $this->userDao = $userDao;
        $this->authService = $authService;
        $this->registerRoute('/auth/login', 'POST', null, 'login');
        $this->registerRoute('/auth/logout', 'GET', '*', 'logout');
    }

    public function login(UserLogin $userLogin): AuthResponse {
        return $this->authService->login($userLogin);
    }

    public function logout() {
        // Invalidate current session
        $accessToken = AuthService::getAuthContext()['accessToken'];
        $this->authService->logout($accessToken);
    }
}

onInit(function () {
    registerController(AuthController::class);
});
