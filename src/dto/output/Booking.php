<?php


class Booking {
    public $bookingId;
    public $createdAt;

    public function __construct(array $rawEntity) {
        $this->bookingId = $rawEntity['bookingId'];
        $this->createdAt = strtotime($rawEntity['createdAt']);
    }
}