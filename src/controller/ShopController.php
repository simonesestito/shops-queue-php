<?php


class ShopController extends BaseController {
    private $shopDao;
    private $userDao;

    public function __construct(ShopDao $shopDao, UserDao $userDao) {
        $this->shopDao = $shopDao;
        $this->userDao = $userDao;
        $this->registerRoute('', 'POST', 'ADMIN', 'addNewShop');
    }

    public static function getBaseUrl(): string {
        return '/shops';
    }

    /**
     * Create a new shop with the related Shop owner account
     * @param NewShopAccount $newShopAccount
     * @return Shop
     */
    public function addNewShop(NewShopAccount $newShopAccount): Shop {
        $shopId = $this->shopDao->insertNewShop($newShopAccount->newShop);
        try {
            $this->userDao->insertShopOwner($newShopAccount->newUser, $shopId);
            return $newShopAccount->newShop->toShop($shopId);
        } catch (DuplicateEntityException $e) {
            // User already exists
            // Revert adding the shop
            $this->shopDao->removeShopById($shopId);
            throw $e;
        }
    }
}

onInit(function () {
    registerController(ShopController::class);
});