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

class FavouriteController extends BaseController {
    private $favouritesDao;

    public function __construct(FavouritesDao $favouritesDao) {
        $this->favouritesDao = $favouritesDao;
        $this->registerRoute('/users/:id/favourites', 'GET', '*', 'getFavouritesOfUser');
        $this->registerRoute('/users/:userId/favourites/:shopId', 'POST', '*', 'addFavourite');
        $this->registerRoute('/users/:userId/favourites/:shopId', 'DELETE', '*', 'removeFavourite');
    }

    /**
     * Get the favourite shops of a user
     * @param $id int User ID
     * @return Shop[]
     * @throws AppHttpException
     */
    public function getFavouritesOfUser(int $id) {
        $authContext = AuthService::getAuthContext();
        if ($authContext['role'] !== 'ADMIN' && $authContext['id'] !== $id)
            throw new AppHttpException(HTTP_NOT_AUTHORIZED);

        $entities = $this->favouritesDao->getFavouritesOfUser($id);
        return array_map(function ($entity) {
            return new Shop($entity);
        }, $entities);
    }

    /**
     * Mark a shop as a user's favourite one
     * @param $userId int
     * @param $shopId int
     * @throws AppHttpException
     */
    public function addFavourite(int $userId, int $shopId) {
        $authContext = AuthService::getAuthContext();
        if ($authContext['role'] !== 'ADMIN' && $authContext['id'] !== $userId)
            throw new AppHttpException(HTTP_NOT_AUTHORIZED);

        try {
            $this->favouritesDao->addFavourite($userId, $shopId);
        } catch (DuplicateEntityException $e) {
        }
    }

    /**
     * Remove a shop from user's favourites
     * @param $userId int
     * @param $shopId int
     * @throws AppHttpException
     */
    public function removeFavourite(int $userId, int $shopId) {
        $authContext = AuthService::getAuthContext();
        if ($authContext['role'] !== 'ADMIN' && $authContext['id'] !== $userId)
            throw new AppHttpException(HTTP_NOT_AUTHORIZED);

        $this->favouritesDao->removeFavourite($userId, $shopId);
    }
}

onInit(function () {
    registerController(FavouriteController::class);
});