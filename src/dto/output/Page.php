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