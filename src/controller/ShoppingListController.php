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

class ShoppingListController extends BaseController {
    private $shoppingListDao;
    private $productDao;
    private $fcmService;

    public function __construct(ShoppingListDao $shoppingListDao, ProductDao $productDao, FcmService $fcmService) {
        $this->shoppingListDao = $shoppingListDao;
        $this->productDao = $productDao;
        $this->fcmService = $fcmService;
        $this->registerRoute('/users/me/lists', 'GET', 'USER', 'getMyLists');
        $this->registerRoute('/shops/me/lists', 'GET', 'OWNER', 'getMyShopLists');
        $this->registerRoute('/lists', 'POST', 'USER', 'addList');
        $this->registerRoute('/lists/:id', 'DELETE', '*', 'deleteList');
        $this->registerRoute('/lists/:id', 'POST', 'OWNER', 'prepareList');
    }

    /**
     * Get the lists of the current user
     * @return ShoppingList[]
     */
    public function getMyLists() {
        $userId = AuthService::getAuthContext()['id'];
        $lists = $this->shoppingListDao->getListsByUserId($userId);
        return array_map(function ($list) {
            return new ShoppingList($list);
        }, $lists);
    }

    /**
     * Get the lists of the current shop owner
     * @return ShoppingList[]
     */
    public function getMyShopLists() {
        $shopId = AuthService::getAuthContext()['shopId'];
        $lists = $this->shoppingListDao->getListsByShopId($shopId);
        return array_map(function ($list) {
            return new ShoppingList($list);
        }, $lists);
    }

    /**
     * Add a new list
     * @param NewShoppingList $newShoppingList
     * @return ShoppingList
     * @throws AppHttpException
     */
    public function addList(NewShoppingList $newShoppingList) {
        // Check that every product is sold by the same shop
        $products = $this->productDao->getProductsByIds($newShoppingList->productIds);
        if (count($products) != count($newShoppingList->productIds)) {
            // Some product IDs aren't known
            throw new AppHttpException(HTTP_NOT_FOUND);
        }
        $shopId = $products[0]['shopId'];
        foreach ($products as $product) {
            if ($product['shopId'] !== $shopId)
                throw new AppHttpException(HTTP_BAD_REQUEST);
        }

        $userId = AuthService::getAuthContext()['id'];
        $id = $this->shoppingListDao->addUserShoppingList($userId, $newShoppingList);
        $entity = $this->shoppingListDao->getListById($id);
        return new ShoppingList($entity);
    }

    /**
     * Delete a list created by the current user
     * @param int $listId
     * @throws AppHttpException
     */
    public function deleteList(int $listId) {
        $authContext = AuthService::getAuthContext();
        $userId = $authContext['id'];
        $userRole = $authContext['role'];
        $userShopId = $authContext['shopId'];

        $entity = $this->shoppingListDao->getListById($listId);
        if ($entity == null)
            throw new AppHttpException(HTTP_NOT_FOUND);
        $shoppingList = new ShoppingList($entity);

        if ($shoppingList->userId == $userId) {
            $this->shoppingListDao->deleteShoppingList($listId);
        } elseif ($userRole == 'OWNER' && $shoppingList->shop->id == $userShopId) {
            if (!$shoppingList->isReady) {
                // Send push notification
                $this->fcmService->sendPayloadToUser(
                    $shoppingList->userId,
                    FCM_TYPE_ORDER_CANCELLED,
                    $shoppingList
                );
            }
            $this->shoppingListDao->deleteShoppingList($listId);
        } else {
            throw new AppHttpException(HTTP_NOT_AUTHORIZED);
        }
    }

    /**
     * Set a shopping list as ready to be retired
     * @param int $listId
     * @return ShoppingList
     * @throws AppHttpException
     */
    public function prepareList(int $listId) {
        $this->shoppingListDao->prepareShoppingList($listId);

        $entity = $this->shoppingListDao->getListById($listId);
        if ($entity == null)
            throw new AppHttpException(HTTP_NOT_FOUND);

        $shoppingList = new ShoppingList($entity);
        if ($shoppingList->shop->id != AuthService::getAuthContext()['shopId'])
            throw new AppHttpException(HTTP_NOT_AUTHORIZED);

        // Send push notification
        $this->fcmService->sendPayloadToUser(
            $shoppingList->userId,
            FCM_TYPE_ORDER_READY,
            $shoppingList
        );

        return new ShoppingList($entity);
    }
}

onInit(function () {
    registerController(ShoppingListController::class);
});