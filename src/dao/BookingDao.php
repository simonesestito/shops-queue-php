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
     * @return int New booking ID
     */
    public function addNewUserBooking(int $userId, int $shopId) {
        return $this->query("INSERT INTO Booking (userId, shopId) VALUES (?, ?)", [
            $userId,
            $shopId
        ]);
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

    /**
     * Get and remove from the queue, the next user waiting for his turn
     * It will only work if the current user is the shop owner of that shop
     * @param int $shopId
     * @param int $ownerId Current user which will be verified to be the owner
     * @return array|null Removed BookingDetail record or null
     */
    public function popShopQueueForOwner(int $shopId, int $ownerId) {
        // Lock table to prevent concurrency issues
        $this->query("LOCK TABLES Booking WRITE");

        try {
            // Join with User to know who is the owner of this shop
            $usersResult = $this->query(
                "SELECT BookingDetail.*
                    FROM BookingDetail
                    JOIN User ON User.shopId = BookingDetail.bookingShopId
                    WHERE bookingShopId = ?
                    AND User.id = ?
                    LIMIT 1", [$shopId, $ownerId]);

            if (empty($usersResult))
                return null;

            $nextUser = $usersResult[0];
            $this->query("DELETE FROM Booking WHERE id = ?", [$nextUser['bookingId']]);
            return $nextUser;
        } finally {
            $this->query("UNLOCK TABLES");
        }
    }
}