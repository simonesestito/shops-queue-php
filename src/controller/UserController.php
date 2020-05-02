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

class UserController extends BaseController {
    private $userDao;
    private $validator;

    public function __construct(UserDao $userDao, Validator $validator) {
        $this->userDao = $userDao;
        $this->validator = $validator;
        $this->registerRoute('/users', 'GET', 'ADMIN', 'listUsers');
        $this->registerRoute('/users', 'POST', null, 'signupUser');
        $this->registerRoute('/users/me', 'GET', '*', 'getCurrentUser');
        $this->registerRoute('/users/:id', 'GET', '*', 'getUserById');
        $this->registerRoute('/users/:id', 'DELETE', 'ADMIN', 'deleteUser');
        $this->registerRoute('/shops/:shopId/owners', 'GET', 'ADMIN', 'getShopOwners');
        $this->registerRoute('/shops/:shopId/owners/:userId', 'PUT', 'ADMIN', 'addOwnerToShop');
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
     * - query: By name
     *
     * @return Page Page of User objects
     */
    public function listUsers(): Page {
        $page = isset($_GET['page']) ? intval($_GET['page']) : 0;
        $query = isset($_GET['query']) ? $_GET['query'] : '';

        $offset = $page * PAGINATION_PAGE_SIZE;

        $daoResult = $this->userDao->getUsers($offset, PAGINATION_PAGE_SIZE, $query);
        $objects = array_map(function ($e) {
            return new User($e);
        }, $daoResult['data']);

        return new Page($page, $daoResult['count'], $objects);
    }

    /**
     * Get the currently logged in user
     * @return User
     * @throws AppHttpException
     */
    public function getCurrentUser() {
        return $this->getUserById(AuthService::getAuthContext()['id']);
    }

    /**
     * Get a user by ID
     * @param $id int User id
     * @return User
     * @throws AppHttpException
     */
    public function getUserById(int $id) {
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
     * Delete a user
     * @param $id int User ID
     */
    public function deleteUser(int $id) {
        $this->userDao->deleteUser($id);
    }

    /**
     * Get the owners of a shop
     * @param $shopId int
     * @return User[]
     */
    public function getShopOwners(int $shopId) {
        $entities = $this->userDao->getOwnersOf($shopId);
        return array_map(function ($e) {
            return new User($e);
        }, $entities);
    }

    /**
     * Add a user as a owner of a shop
     * It must already be of type OWNER
     * @param $shopId int
     * @param $userId int
     * @throws AppHttpException
     */
    public function addOwnerToShop(int $shopId, int $userId) {
        $user = $this->userDao->getUserById($userId);
        if ($user['role'] !== 'OWNER')
            throw new AppHttpException(HTTP_BAD_REQUEST);

        $this->userDao->addOwnerToShop($userId, $shopId);
    }
}

onInit(function () {
    registerController(UserController::class);
});