<?php


class FavouriteController extends BaseController {
    private $favouritesDao;

    public function __construct(FavouritesDao $favouritesDao) {
        $this->favouritesDao = $favouritesDao;
        $this->registerRoute('/users/:id/favourites', 'GET', '*', 'getFavouritesOfUser');
        $this->registerRoute('/users/:userId/favourites/:shopId', 'POST', '*', 'addFavourite');
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

        $this->favouritesDao->addFavourite($userId, $shopId);
    }
}

onInit(function () {
    registerController(FavouriteController::class);
});