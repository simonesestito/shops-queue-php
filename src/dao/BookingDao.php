<?php

require_once __DIR__ . '/Dao.php';


class BookingDao extends Dao {
    /**
     * Get all bookings for a given shop
     * sorted by creation date.
     * It includes info about the user
     * @param $shopId int
     * @return array
     */
    public function getBookingsByShopId(int $shopId): array {
        return $this->query("SELECT * FROM BookingDetail WHERE bookingShopId = ?", [$shopId]);
    }

    /**
     * Get all bookings made by a given user
     * sorted by creation date.
     * It includes info about the shop
     * @param int $userId
     * @return array
     */
    public function getBookingsByUserId(int $userId): array {
        return $this->query("SELECT * FROM BookingDetail WHERE userId = ?", [$userId]);
    }

    /**
     * Add a new booking to the selected shop,
     * assuming the current timestamp as the creation date
     * @param int $userId
     * @param int $shopId
     * @return array Booking and shop record, like getBookingsByUserId()
     * @see BookingDao::getBookingsByUserId()
     */
    public function addNewUserBooking(int $userId, int $shopId) {
        $bookingId = $this->query("INSERT INTO Booking (userId, shopId) VALUES (?, ?)", [
            $userId,
            $shopId
        ]);
        $result = $this->query("SELECT * FROM BookingDetail WHERE bookingId = ?", [$bookingId]);
        return $result[0];
    }
}