<?php


class Booking {
    public $bookingId;
    public $createdAt;
    public $user;
    public $shop;

    /**
     * Booking constructor.
     * @param array $rawEntity Record of BookingDetail view
     */
    public function __construct(array $rawEntity) {
        $this->bookingId = $rawEntity['bookingId'];
        $this->createdAt = strtotime($rawEntity['createdAt']);

        // Undo aliases
        $rawEntity['id'] = $rawEntity['userId'];
        $this->user = new User($rawEntity);

        $rawEntity['id'] = $rawEntity['bookingShopId'];
        $rawEntity['name'] = $rawEntity['shopName'];
        $this->shop = new Shop($rawEntity);
    }
}