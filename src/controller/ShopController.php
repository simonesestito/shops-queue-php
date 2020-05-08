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

class ShopController extends BaseController {
    private $shopDao;
    private $validator;

    public function __construct(ShopDao $shopDao, Validator $validator) {
        $this->shopDao = $shopDao;
        $this->validator = $validator;
        $this->registerRoute('/shops', 'GET', 'ADMIN', 'listShops');
        $this->registerRoute('/shops', 'POST', 'ADMIN', 'addNewShop');
        $this->registerRoute('/shops/nearby', 'GET', '*', 'findNearShops');
        $this->registerRoute('/shops/me', 'GET', 'OWNER', 'getOwnShop');
        $this->registerRoute('/shops/:id', 'GET', '*', 'getShopById');
        $this->registerRoute('/shops/:id', 'PUT', 'ADMIN', 'updateShop');
        $this->registerRoute('/shops/:id', 'DELETE', 'ADMIN', 'deleteShop');
    }

    /**
     * Create a new shop
     * The related Shop owner account must be created separately
     * @param NewShop $newShop
     * @return Shop
     */
    public function addNewShop(NewShop $newShop): Shop {
        $shopId = $this->shopDao->insertNewShop($newShop);
        $entity = $this->shopDao->getShopById($shopId);
        return new Shop($entity);
    }

    /**
     * List all the shops, ordered alphabetically
     * It uses pagination.
     * @return Page Page of Shops
     */
    public function listShops() {
        $query = isset($_GET['query']) ? $_GET['query'] : '';
        $page = isset($_GET['page']) ? intval($_GET['page']) : 0;
        $offset = $page * PAGINATION_PAGE_SIZE;

        $daoResult = $this->shopDao->listShops($offset, PAGINATION_PAGE_SIZE, $query);
        $objects = array_map(function ($e) {
            return new Shop($e);
        }, $daoResult['data']);

        return new Page($page, $daoResult['count'], $objects);
    }

    /**
     * Find shops near the user's location.
     *
     * It requires the following GET params:
     * - lat: the user's X coordinate
     * - lon: the user's Y coordinate
     *
     * To search by name, you can use an additional "query" GET param.
     * @return ShopWithDistance[]
     */
    public function findNearShops(): array {
        // Validate required get params
        $this->validator->validate([
            'lat' => Validator::filterAs(FILTER_VALIDATE_FLOAT),
            'lon' => Validator::filterAs(FILTER_VALIDATE_FLOAT),
            'query' => Validator::optional('is_string'),
        ], $_GET);

        $lat = floatval($_GET['lat']);
        $lon = floatval($_GET['lon']);
        $query = isset($_GET['query']) ? trim($_GET['query']) : '';

        $daoResult = $this->shopDao->findShops($lat, $lon, $query);
        return array_map(function ($e) {
            return new ShopWithDistance($e);
        }, $daoResult);
    }

    /**
     * Get the shop associated with the current shop owner
     * @return Shop
     * @throws AppHttpException
     */
    public function getOwnShop() {
        $shopId = AuthService::getAuthContext()['shopId'];
        return $this->getShopById($shopId);
    }

    /**
     * Get a shop by its ID
     * @param int $id
     * @return Shop Found shop
     * @throws AppHttpException
     */
    public function getShopById(int $id) {
        $authContext = AuthService::getAuthContext();
        if ($authContext['shopId'] !== $id && $authContext['role'] !== 'ADMIN') {
            throw new AppHttpException(HTTP_NOT_AUTHORIZED);
        }

        $entity = $this->shopDao->getShopById($id);
        if ($entity === null)
            throw new AppHttpException(HTTP_NOT_FOUND);
        return new Shop($entity);
    }

    /**
     * Update an existing shop
     * @param $id int
     * @param NewShop $newShop
     * @return Shop Updated shop
     * @throws AppHttpException
     */
    public function updateShop(int $id, NewShop $newShop) {
        $this->shopDao->updateShop($id, $newShop);
        $entity = $this->shopDao->getShopById($id);
        if ($entity === null)
            throw new AppHttpException(HTTP_NOT_FOUND);
        return new Shop($entity);
    }

    /**
     * Delete an existing shop
     * @param $id int
     */
    public function deleteShop(int $id) {
        $this->shopDao->removeShopById($id);
    }
}

onInit(function () {
    registerController(ShopController::class);
});
