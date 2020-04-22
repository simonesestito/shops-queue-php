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
        $this->registerRoute('/logout', 'GET', '*', 'logout');
    }

    public static function getBaseUrl(): string {
        return '/auth';
    }

    public function signupUser(NewUser $newUser): AuthResponse {
        $this->userDao->insertNewUser($newUser);

        // Login immediately after sign up
        return $this->login(new UserLogin([
            'email' => $newUser->email,
            'password' => $newUser->password,
        ]));
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