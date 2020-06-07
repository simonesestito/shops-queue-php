<?php
/**
 * Copyright 2020 Simone Sestito
 * This file is part of Shops Queue.
 *
 * Shops Queue is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Shops Queue is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with Shops Queue.  If not, see <http://www.gnu.org/licenses/>.
 */

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
     * Get all bookings made by a given user, sorted by creation date.
     * @param int $userId
     * @return array BookingDetailQueueCount records
     */
    public function getBookingsByUserId(int $userId): array {
        return $this->query("SELECT * FROM BookingDetailQueueCount WHERE userId = ?", [$userId]);
    }

    /**
     * Get the first bookings from the queue of a shop
     * @param int $shopId
     * @param array $positions An array with the positions in the queue to return
     * @return array BookingDetailQueueCount records
     */
    public function getFirstBookingsForShop(int $shopId, array $positions) {
        $arrayTemplate = arraySqlArg(count($positions));
        $args = array_merge([$shopId], $positions);
        return $this->query("SELECT *
                                    FROM BookingDetailQueueCount
                                    WHERE bookingShopId = ?
                                    AND queueCount IN $arrayTemplate", $args);
    }

    /**
     * Add a new booking to the selected shop,
     * assuming the current timestamp as the creation date
     * @param int $userId
     * @param int $shopId
     * @return int New booking ID
     */
    public function addNewUserBooking(int $userId, int $shopId) {
        // Check if there's already another booking by this user on this shop
        $existing = $this->query("SELECT id FROM Booking WHERE userId = ? AND shopId = ? AND finished = FALSE", [
            $userId,
            $shopId
        ]);
        if (count($existing) > 0) {
            throw new DuplicateEntityException();
        }

        return $this->query("INSERT INTO Booking (userId, shopId) VALUES (?, ?)", [
            $userId,
            $shopId
        ]);
    }

    /**
     * Get a booking with the given ID
     * @param int $id Booking ID
     * @return array|null BookingDetailQueueCount single record or null
     */
    public function getBookingById(int $id) {
        $result = $this->query("SELECT * FROM BookingDetailQueueCount WHERE bookingId = ?", [$id]);
        return @$result[0];
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
     */
    public function popShopQueue(int $shopId) {
        // Lock table to prevent concurrency issues
        try {
            $this->query("LOCK TABLES Booking WRITE");
        } catch (Throwable $ignored) {
        }

        try {
            // Join with User to know who is the owner of this shop
            $results = $this->query(
                "SELECT bookingId
                    FROM BookingDetail
                    JOIN User ON User.shopId = BookingDetail.bookingShopId
                    WHERE bookingShopId = ?
                    ORDER BY createdAt
                    LIMIT 1", [$shopId]);

            if (!empty($results))
                $this->query("UPDATE Booking SET finished = TRUE WHERE id = ?", [$results[0]['bookingId']]);
        } finally {
            try {
                $this->query("UNLOCK TABLES");
            } catch (Throwable $ignored) {
            }
        }
    }

    /**
     * Delete all the bookings for a specific shop
     * @param int $shopId
     */
    public function deleteBookingsByShop(int $shopId) {
        $this->query("DELETE FROM Booking WHERE shopId = ?", [$shopId]);
    }
}