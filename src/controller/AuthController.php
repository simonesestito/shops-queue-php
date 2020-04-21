<?php

require_once __DIR__ . '/BaseController.php';


class AuthController extends BaseController {
    private $userDao;
    private $authService;

    public function __construct(UserDao $userDao, AuthService $authService) {
        $this->userDao = $userDao;
        $this->authService = $authService;
        $this->registerRoute('/signup', 'POST', null, 'signupUser');
        $this->registerRoute('/login', 'POST', null, 'login');
    }

    public static function getBaseUrl(): string {
        return '/auth';
    }

    public function signupUser(NewUser $newUser) {
        $this->userDao->insertNewUser($newUser);
        // TODO Login and return
    }

    public function login(UserLogin $userLogin): AuthResponse {
        return $this->authService->login($userLogin);
    }
}

onInit(function () {
    registerController(AuthController::class);
});