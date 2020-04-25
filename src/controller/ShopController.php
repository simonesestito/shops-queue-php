<?php


class ShopController extends BaseController {
    private $shopDao;
    private $userDao;
    private $validator;

    public function __construct(ShopDao $shopDao, UserDao $userDao, Validator $validator) {
        $this->shopDao = $shopDao;
        $this->userDao = $userDao;
        $this->validator = $validator;
        $this->registerRoute('/shops', 'POST', 'ADMIN', 'addNewShop');
        $this->registerRoute('/shops/nearby', 'GET', '*', 'findNearShops');
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

    /**
     * Find shops near the user's location.
     *
     * It requires the following GET params:
     * - page: the page number, to use pagination
     * - lat: the user's X coordinate
     * - lon: the user's Y coordinate
     *
     * To search by name, you can use an additional "query" GET param.
     * @return Page Page of Shop objects
     */
    public function findNearShops(): Page {
        // Validate required get params
        $this->validator->validate([
            'page' => Validator::optional(Validator::filterAs(FILTER_VALIDATE_INT)),
            'lat' => Validator::filterAs(FILTER_VALIDATE_FLOAT),
            'lon' => Validator::filterAs(FILTER_VALIDATE_FLOAT),
            'query' => Validator::optional('is_string'),
        ], $_GET);

        $page = isset($_GET['page']) ? intval($_GET['page']) : 0;
        $lat = floatval($_GET['lat']);
        $lon = floatval($_GET['lon']);
        $query = isset($_GET['query']) ? trim($_GET['query']) : '';

        $offset = $page * PAGINATION_PAGE_SIZE;

        $daoResult = $this->shopDao->findShops($lat, $lon, $offset, PAGINATION_PAGE_SIZE, $query);
        $objects = array_map(function ($e) {
            return new ShopWithDistance($e);
        }, $daoResult['data']);

        return new Page($page, $daoResult['count'], $objects);
    }
}

onInit(function () {
    registerController(ShopController::class);
});