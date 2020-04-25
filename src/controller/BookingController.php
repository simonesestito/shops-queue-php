<?php


class BookingController extends BaseController {
    private $bookingDao;

    public function __construct(BookingDao $bookingDao) {
        $this->bookingDao = $bookingDao;
        $this->registerRoute('/shops/:shopId/bookings', 'POST', 'USER', 'addBookingToShop');
        $this->registerRoute('/shops/:shopId/bookings', 'GET', '*', 'getBookingsByShop');
        $this->registerRoute('/users/:userId/bookings', 'GET', '*', 'getBookingsByUser');
        $this->registerRoute('/bookings/:id', 'GET', '*', 'getBookingById');
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

    /**
     * Get a single booking by ID
     * If the current user isn't allowed to access the requested Booking,
     * it will throw an error
     * @param $id mixed
     * @return Booking
     * @throws AppHttpException
     */
    public function getBookingById($id) {
        $id = intval($id);

        $entity = $this->bookingDao->getBookingById($id);
        if ($entity === null)
            throw new AppHttpException(HTTP_NOT_FOUND);
        $booking = new Booking($entity);

        $authContext = AuthService::getAuthContext();
        $currentRole = $authContext['role'];
        $currentUser = $authContext['id'];
        $ownerShopId = $authContext['shopId'];

        if ($currentRole === 'USER' && $currentUser !== $booking->user->id)
            throw new AppHttpException(HTTP_NOT_AUTHORIZED);
        if ($currentRole === 'OWNER' && $ownerShopId !== $booking->shop->id)
            throw new AppHttpException(HTTP_NOT_AUTHORIZED);

        return $booking;
    }
}

onInit(function () {
    registerController(BookingController::class);
});