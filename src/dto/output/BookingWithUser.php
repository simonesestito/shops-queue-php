<?php


class BookingWithUser {
    public $booking;
    public $user;

    public function __construct(array $rawEntity) {
        $this->booking = new Booking($rawEntity);

        // Adjust ID name
        $rawEntity['id'] = $rawEntity['userId'];
        $this->user = new User($rawEntity);
    }
}