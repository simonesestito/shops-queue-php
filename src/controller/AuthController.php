<?php

require_once __DIR__ . '/BaseController.php';


class AuthController extends BaseController {
    private $userDao;
    private $authService;

    public function __construct(UserDao $userDao, AuthService $authService) {
        $this->userDao = $userDao;
        $this->authService = $authService;

        $this->registerRoute('/auth/signup', 'POST', null, 'signupUser');
        $this->registerRoute('/auth/login', 'POST', null, 'login');
        $this->registerRoute('/auth/logout', 'GET', '*', 'logout');
    }

    /**
     * Create a new user
     * @param NewUser $newUser
     * @return User
     * @throws AppHttpException If admin required
     */
    public function signupUser(NewUser $newUser): User {
        // Admin check
        $adminRequired = $newUser->role !== 'USER';
        if ($adminRequired) {
            // Only admins can create users with roles different from USER
            $authContext = AuthService::getAuthContext();
            if ($authContext === null)
                throw new AppHttpException(HTTP_NOT_LOGGED_IN);
            if ($authContext['role'] !== 'ADMIN')
                throw new AppHttpException(HTTP_NOT_AUTHORIZED);
        }

        if ($newUser->role === 'OWNER' && is_null($newUser->shopId)) {
            // Owners must have a shop ID
            throw new AppHttpException(HTTP_BAD_REQUEST, 'Missing shop ID');
        }

        $userId = $this->userDao->insertNewUser($newUser);
        return new User($this->userDao->getUserById($userId));
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
