<?php

define('PAGINATION_PAGE_SIZE', 20);

/**
 * Class Page
 * Used to implement pagination in API responses
 */
class Page {
    /**
     * Current page number
     */
    public $page;

    /**
     * Total number of pages
     */
    public $totalPages;

    /**
     * Total number of items
     */
    public $totalItems;

    /**
     * Actual data
     */
    public $data;

    public function __construct(int $page, int $totalItems, $data) {
        $this->page = $page;
        $this->totalItems = $totalItems;
        $this->totalPages = (int)ceil($totalItems / PAGINATION_PAGE_SIZE);
        $this->data = $data;
    }
}