<?php

require_once __DIR__ . '/Dao.php';


class BookingDao extends Dao {
    /**
     * Get all bookings for a given shop, sorted by creation date.
     * @param $shopId int
     * @return array BookingDetail records
     */
    public function getBookingsByShopId(int $shopId): array {
        return $this->query("SELECT * FROM BookingDetail WHERE bookingShopId = ?", [$shopId]);
    }

    /**
     * Get the number of bookings for that shop
     * @param $id int Shop ID
     * @return int
     */
    public function countBookingsByShopId($id) {
        return $this->query("SELECT COUNT(*) AS c FROM Booking WHERE shopId = ?", [$id])[0]['c'];
    }

    /**
     * Get all bookings made by a given user, sorted by creation date.
     * @param int $userId
     * @return array BookingDetail records
     */
    public function getBookingsByUserId(int $userId): array {
        return $this->query("SELECT * FROM BookingDetail WHERE userId = ?", [$userId]);
    }

    /**
     * Add a new booking to the selected shop,
     * assuming the current timestamp as the creation date
     * @param int $userId
     * @param int $shopId
     * @return array BookingDetail single record
     */
    public function addNewUserBooking(int $userId, int $shopId) {
        $bookingId = $this->query("INSERT INTO Booking (userId, shopId) VALUES (?, ?)", [
            $userId,
            $shopId
        ]);
        return $this->getBookingById($bookingId);
    }

    /**
     * Get a booking with thr given ID
     * @param int $id Booking ID
     * @return array|null BookingDetail single record or null
     */
    public function getBookingById(int $id) {
        $result = $this->query("SELECT * FROM BookingDetail WHERE bookingId = ?", [$id]);
        return @$result[0];
    }

    /**
     * Delete a booking from the database only if the user matches
     * @param $userId int Expected user who made this booking
     * @param $id int Booking ID
     */
    public function deleteBookingByIdForUser(int $userId, int $id) {
        $this->query("DELETE FROM Booking WHERE id = ? AND userId = ?", [$id, $userId]);
    }

    /**
     * Delete a booking from the database unconditionally
     * @param $id int Booking ID
     */
    public function deleteBookingById(int $id) {
        $this->query("DELETE FROM Booking WHERE id = ?", [$id]);
    }
}