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
        $this->registerRoute('/test', 'GET', '*', 'testLogin');
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

    public function testLogin(): User {
        $userId = $GLOBALS['auth']['id'];
        $dbUser = $this->userDao->getUserById($userId);
        return User::fromEntity($dbUser);
    }
}

onInit(function () {
    registerController(AuthController::class);
});