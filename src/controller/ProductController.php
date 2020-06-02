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

class ProductController extends BaseController {
    private $productDao;

    public function __construct(ProductDao $productDao) {
        $this->productDao = $productDao;
        $this->registerRoute('/shops/:id/products', 'GET', '*', 'getShopProducts');
        $this->registerRoute('/products', 'POST', 'OWNER', 'addShopProduct');
        $this->registerRoute('/products/:id', 'PUT', 'OWNER', 'editProduct');
        $this->registerRoute('/products/:id', 'DELETE', 'OWNER', 'deleteProduct');
    }

    /**
     * @param int $shopId
     * @return Product[]
     */
    public function getShopProducts(int $shopId): array {
        $entities = $this->productDao->getProductsByShopId($shopId);
        return array_map(function ($entity) {
            return new Product($entity);
        }, $entities);
    }

    /**
     * @param NewProduct $newProduct
     * @return Product
     */
    public function addShopProduct(NewProduct $newProduct): Product {
        $shopId = AuthService::getAuthContext()['shopId'];
        $id = $this->productDao->addProduct($shopId, $newProduct);
        $entity = $this->productDao->getProductById($id);
        return new Product($entity);
    }

    /**
     * @param int $id
     * @param NewProduct $newProduct
     * @return Product
     */
    public function editProduct(int $id, NewProduct $newProduct): Product {
        $shopId = AuthService::getAuthContext()['shopId'];
        $this->productDao->editProduct($id, $shopId, $newProduct);
        $newEntity = $this->productDao->getProductById($id);
        return new Product($newEntity);
    }

    /**
     * @param int $id
     */
    public function deleteProduct(int $id) {
        $shopId = AuthService::getAuthContext()['shopId'];
        $this->productDao->deleteProduct($id, $shopId);
    }
}

onInit(function () {
    registerController(ProductController::class);
});