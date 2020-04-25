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
        $sql = "SELECT *,
                Booking.id AS bookingId,
                User.id AS userId
                FROM Booking
                JOIN User ON Booking.userId = User.id
                WHERE Booking.shopId = ?
                ORDER BY createdAt";
        return $this->query($sql, [$shopId]);
    }

    /**
     * Get all bookings made by a given user
     * sorted by creation date.
     * It includes info about the shop
     * @param int $userId
     * @return array
     */
    public function getBookingsByUserId(int $userId): array {
        $sql = "SELECT *,
                Booking.id AS bookingId,
                Shop.id AS shopId
                FROM Booking
                JOIN Shop ON Booking.shopId = Shop.id
                WHERE Booking.userId = ?
                ORDER BY createdAt";
        return $this->query($sql, [$userId]);
    }

    /**
     * Add a new booking to the selected shop,
     * assuming the current timestamp as the creation date
     * @param int $userId
     * @param int $shopId
     * @return int Created booking's ID
     */
    public function addNewBooking(int $userId, int $shopId) {
        return $this->query("INSERT INTO Booking (userId, shopId) VALUES (?, ?)", [
            $userId,
            $shopId
        ]);
    }
}