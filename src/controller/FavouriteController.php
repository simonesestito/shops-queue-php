<?php


class FavouriteController extends BaseController {
    private $favouritesDao;

    public function __construct(FavouritesDao $favouritesDao) {
        $this->favouritesDao = $favouritesDao;
        $this->registerRoute('/users/:id/favourites', 'GET', '*', 'getFavouritesOfUser');
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
}

onInit(function () {
    registerController(FavouriteController::class);
});