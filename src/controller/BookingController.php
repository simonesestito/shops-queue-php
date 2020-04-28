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

class BookingController extends BaseController {
    private $bookingDao;

    public function __construct(BookingDao $bookingDao) {
        $this->bookingDao = $bookingDao;
        $this->registerRoute('/shops/:shopId/bookings', 'POST', 'USER', 'addBookingToShop');
        $this->registerRoute('/shops/:shopId/bookings', 'GET', '*', 'getBookingsByShop');
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
        $bookingId = $this->bookingDao->addNewUserBooking($userId, $shopId);
        $entity = $this->bookingDao->getBookingById($bookingId);

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