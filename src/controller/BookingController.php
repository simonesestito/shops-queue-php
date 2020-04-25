<?php


class BookingController extends BaseController {
    private $bookingDao;

    public function __construct(BookingDao $bookingDao) {
        $this->bookingDao = $bookingDao;
        $this->registerRoute('/shops/:shopId/bookings', 'POST', 'USER', 'addBookingToShop');
    }

    /**
     * Add a booking to the selected shop, made by the current user
     * @param $shopId
     * @return BookingWithShop
     */
    public function addBookingToShop($shopId): BookingWithShop {
        // IDs are integers
        $shopId = intval($shopId);

        // Get current user
        $userId = AuthService::getAuthContext()['id'];
        $entity = $this->bookingDao->addNewUserBooking($userId, $shopId);

        return new BookingWithShop($entity);
    }
}

onInit(function () {
    registerController(BookingController::class);
});