<?php


class UserController extends BaseController {
    private $userDao;

    public function __construct(UserDao $userDao) {
        $this->userDao = $userDao;
        $this->registerRoute('/users', 'POST', null, 'signupUser');
        $this->registerRoute('/users/:id', 'GET', '*', 'getUserById');
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

    /**
     * Get a user by ID
     * @param $id mixed User id
     * @return User
     * @throws AppHttpException
     */
    public function getUserById($id) {
        $id = intval($id);

        $authContext = AuthService::getAuthContext();
        if ($authContext['id'] !== $id && $authContext['role'] !== 'ADMIN') {
            throw new AppHttpException(HTTP_NOT_AUTHORIZED);
        }

        $entity = $this->userDao->getUserById($id);
        if ($entity === null) {
            throw new AppHttpException(HTTP_NOT_FOUND);
        }

        return new User($entity);
    }
}

onInit(function () {
    registerController(UserController::class);
});