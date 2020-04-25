<?php


class BookingController extends BaseController {
    private $bookingDao;

    public function __construct(BookingDao $bookingDao) {
        $this->bookingDao = $bookingDao;
        $this->registerRoute('/shops/:shopId/bookings', 'POST', 'USER', 'addBookingToShop');
        $this->registerRoute('/shops/:shopId/bookings', 'GET', '*', 'getBookingsByShop');
        $this->registerRoute('/users/:userId/bookings', 'GET', '*', 'getBookingsByUser');
    }

    /**
     * Add a booking to the selected shop, made by the current user
     * @param $shopId
     * @return Booking New booking
     */
    public function addBookingToShop($shopId): Booking {
        // IDs are integers
        $shopId = intval($shopId);

        // Get current user
        $userId = AuthService::getAuthContext()['id'];
        $entity = $this->bookingDao->addNewUserBooking($userId, $shopId);

        return new Booking($entity);
    }

    /**
     * Get the bookings of a shop
     * Owners can only access their shop's bookings
     * Admins can access everything
     * @param $shopId mixed
     * @return Booking[]
     * @throws AppHttpException
     */
    public function getBookingsByShop($shopId) {
        $shopId = intval($shopId);

        // Check user role
        $authContext = AuthService::getAuthContext();
        $userRole = $authContext['role'];
        $ownerShopId = $authContext['shopId'];
        if ($userRole !== 'ADMIN' && $ownerShopId !== $shopId) {
            throw new AppHttpException(HTTP_NOT_AUTHORIZED);
        }

        $entities = $this->bookingDao->getBookingsByShopId($shopId);
        return array_map(function ($entity) {
            return new Booking($entity);
        }, $entities);
    }

    /**
     * Get the bookings made by a user
     * Users can only access the ones they created
     * Admins can access everything
     * @param $userId mixed
     * @return Booking[]
     * @throws AppHttpException
     */
    public function getBookingsByUser($userId) {
        $userId = intval($userId);

        // Check user role
        $authContext = AuthService::getAuthContext();
        $userRole = $authContext['role'];
        $currentUser = $authContext['id'];
        if ($userRole !== 'ADMIN' && $currentUser !== $userId) {
            throw new AppHttpException(HTTP_NOT_AUTHORIZED);
        }

        $entities = $this->bookingDao->getBookingsByUserId($userId);
        return array_map(function ($entity) {
            return new Booking($entity);
        }, $entities);
    }
}

onInit(function () {
    registerController(BookingController::class);
});