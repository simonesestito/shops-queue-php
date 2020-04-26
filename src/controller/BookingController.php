<?php


class BookingController extends BaseController {
    private $bookingDao;

    public function __construct(BookingDao $bookingDao) {
        $this->bookingDao = $bookingDao;
        $this->registerRoute('/shops/:shopId/bookings', 'POST', 'USER', 'addBookingToShop');
        $this->registerRoute('/shops/:shopId/bookings', 'GET', '*', 'getBookingsByShop');
        $this->registerRoute('/shops/:shopId/bookings/count', 'GET', '*', 'getShopBookingsCount');
        $this->registerRoute('/shops/:shopId/bookings/next', 'POST', 'OWNER', 'callNextUser');
        $this->registerRoute('/users/:userId/bookings', 'GET', '*', 'getBookingsByUser');
        $this->registerRoute('/bookings/:id', 'DELETE', '*', 'deleteBooking');
    }

    /**
     * Add a booking to the selected shop, made by the current user
     * @param $shopId int
     * @return Booking New booking
     */
    public function addBookingToShop(int $shopId): Booking {
        // Get current user
        $userId = AuthService::getAuthContext()['id'];
        $entity = $this->bookingDao->addNewUserBooking($userId, $shopId);

        return new Booking($entity);
    }

    /**
     * Get the bookings of a shop
     * Owners can only access their shop's bookings
     * Admins can access everything
     * @param $shopId int
     * @return Booking[]
     * @throws AppHttpException
     */
    public function getBookingsByShop(int $shopId) {
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
     * @param $userId int
     * @return Booking[]
     * @throws AppHttpException
     */
    public function getBookingsByUser(int $userId) {
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
     * Delete a booking by its ID, if authorized
     * @param $id int Booking ID
     */
    public function deleteBooking(int $id) {
        $authContext = AuthService::getAuthContext();
        if ($authContext['role'] === 'ADMIN')
            $this->bookingDao->deleteBookingById($id);
        else
            $this->bookingDao->deleteBookingByIdForUser($authContext['id'], $id);
    }

    /**
     * Get the amount of people in queue in that shop
     * This endpoint can be used by user who couldn't access full bookings info (only owners and admins can),
     * but they just need to know the amount of people.
     * @param $id int Shop ID
     * @return array
     */
    public function getShopBookingsCount(int $id) {
        $count = $this->bookingDao->countBookingsByShopId($id);
        return ['count' => $count];
    }

    /**
     * Call the next user in the shops queue.
     *
     * NOTE: This method returns null if there's no one in the queue
     * A client must be aware of this.
     *
     * @param $id int Shop ID
     * @return Booking|null
     */
    public function callNextUser(int $id) {
        $calledUser = $this->bookingDao->popShopQueueForOwner($id, AuthService::getAuthContext()['id']);
        if ($calledUser === null)
            // No users in the queue
            return null;

        return new Booking($calledUser);
    }
}

onInit(function () {
    registerController(BookingController::class);
});