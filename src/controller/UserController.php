<?php


class UserController extends BaseController {
    private $userDao;
    private $validator;

    public function __construct(UserDao $userDao, Validator $validator) {
        $this->userDao = $userDao;
        $this->validator = $validator;
        $this->registerRoute('/users', 'GET', 'ADMIN', 'listUsers');
        $this->registerRoute('/users', 'POST', null, 'signupUser');
        $this->registerRoute('/users/me', 'GET', '*', 'getSelfUser');
        $this->registerRoute('/users/:id', 'GET', '*', 'getUserById');
        $this->registerRoute('/users/:id', 'DELETE', 'ADMIN', 'deleteUser');
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
     * List all users.
     *
     * Accepted GET params:
     * - page: the page number, to use pagination
     * - role
     * - shopId
     *
     * @return Page Page of User objects
     */
    public function listUsers(): Page {
        // Validate required get params
        $this->validator->validate([
            'page' => Validator::optional(Validator::filterAs(FILTER_VALIDATE_INT)),
            'role' => Validator::optional(Validator::isIn(DB_USER_ROLES)),
            'shopId' => Validator::optional(Validator::filterAs(FILTER_VALIDATE_INT)),
        ], $_GET);

        $page = isset($_GET['page']) ? intval($_GET['page']) : 0;
        $role = @$_GET['role'];
        $shopId = isset($_GET['shopId']) ? intval($_GET['shopId']) : null;

        $offset = $page * PAGINATION_PAGE_SIZE;

        $daoResult = $this->userDao->getUsers($offset, PAGINATION_PAGE_SIZE, $role, $shopId);
        $objects = array_map(function ($e) {
            return new User($e);
        }, $daoResult['data']);

        return new Page($page, $daoResult['count'], $objects);
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

    /**
     * Get the currently logged in user
     * @return User
     * @throws AppHttpException
     */
    public function getSelfUser() {
        return $this->getUserById(AuthService::getAuthContext()['id']);
    }

    /**
     * Delete a user
     * @param $id mixed User ID
     */
    public function deleteUser($id) {
        $id = intval($id);
        $this->userDao->deleteUser($id);
    }
}

onInit(function () {
    registerController(UserController::class);
});