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
        $this->registerRoute('/users', 'POST', 'ADMIN', 'signupUser');
        $this->registerRoute('/users/me', 'GET', '*', 'getCurrentUser');
        $this->registerRoute('/users/:id', 'GET', '*', 'getUserById');
        $this->registerRoute('/users/:id', 'DELETE', 'ADMIN', 'deleteUser');
        $this->registerRoute('/users/:id', 'PUT', 'ADMIN', 'updateUser');
    }

    /**
     * Create a new user
     * @param NewUser $newUser
     * @return UserDetails
     * @throws AppHttpException
     */
    public function signupUser(NewUser $newUser): UserDetails {
        if ($newUser->role === 'OWNER' && is_null($newUser->shopId)) {
            // Owners must have a shop ID
            throw new AppHttpException(HTTP_BAD_REQUEST, 'Missing shop ID');
        }

        $userId = $this->userDao->insertNewUser($newUser);
        return new UserDetails($this->userDao->getUserById($userId));
    }


    /**
     * List all users.
     *
     * Accepted GET params:
     * - page: the page number, to use pagination
     * - query: By name
     * - shopId
     *
     * @return Page Page of UserDetails objects
     */
    public function listUsers(): Page {
        $page = isset($_GET['page']) ? intval($_GET['page']) : 0;
        $shopId = isset($_GET['shopId']) ? intval($_GET['shopId']) : null;
        $query = isset($_GET['query']) ? $_GET['query'] : '';

        $offset = $page * PAGINATION_PAGE_SIZE;

        $daoResult = $this->userDao->getUsers($offset, PAGINATION_PAGE_SIZE, $query, $shopId);
        $objects = array_map(function ($e) {
            return new UserDetails($e);
        }, $daoResult['data']);

        return new Page($page, $daoResult['count'], $objects);
    }

    /**
     * Get the currently logged in user
     * @return UserDetails
     * @throws AppHttpException
     */
    public function getCurrentUser() {
        return $this->getUserById(AuthService::getAuthContext()['id']);
    }

    /**
     * Get a user by ID
     * @param $id int User id
     * @return UserDetails
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

        return new UserDetails($entity);
    }

    /**
     * Delete a user
     * @param $id int User ID
     */
    public function deleteUser(int $id) {
        $this->userDao->deleteUser($id);
    }

    /**
     * Update a user
     * @param int $id User ID
     * @param UserUpdate $update
     * @return UserDetails Updated user
     * @throws AppHttpException
     */
    public function updateUser(int $id, UserUpdate $update) {
        // Check role and shop ID
        $isOwner = $update->role === 'OWNER';
        $shopDefined = $update->shopId !== null;
        if ($isOwner !== $shopDefined) {
            throw new AppHttpException(HTTP_BAD_REQUEST);
        }

        $this->userDao->updateUser($id, $update);
        return new UserDetails($this->userDao->getUserById($id));
    }
}

onInit(function () {
    registerController(UserController::class);
});