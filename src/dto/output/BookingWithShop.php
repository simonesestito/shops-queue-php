<?php


class BookingWithShop {
    public $booking;
    public $shop;

    public function __construct(array $rawEntity) {
        $this->booking = new Booking($rawEntity);

        // Adjust ID name
        $rawEntity['id'] = $rawEntity['bookingShopId'];
        $this->shop = new Shop($rawEntity);
    }
}