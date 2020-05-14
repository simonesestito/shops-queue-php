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

class Dao {
    /** @var $db mysqli */
    protected $db;

    public function __construct(mysqli $db) {
        $this->db = $db;
    }

    /**
     * Execute a query.
     * It bind the given parameters,
     * and handles known MySQL errno
     * @param string $query SQL query
     * @param mixed[] $params Params to bind to the query
     * @return array|int Array of found records, or ID of latest INSERT
     * @throws DuplicateEntityException
     */
    protected function query(string $query, array $params = []) {
        $statement = $this->db->prepare($query);
        if ($statement === false) {
            throw new RuntimeException($this->db->error);
        }

        if (!empty($params)) {
            $types = '';
            foreach ($params as &$param) {
                if (is_string($param) || $param === null)
                    $types .= 's';
                elseif (is_int($param))
                    $types .= 'i';
                elseif (is_bool($param)) {
                    $types .= 'i';
                    $param = $param ? 1 : 0;
                } elseif (is_double($param))
                    $types .= 'd';
                else
                    throw new RuntimeException("Unknown type for '$param'");
            }

            $statement->bind_param($types, ...$params);
        }

        $success = $statement->execute();
        if ($success) {
            // Return the result
            $result = $statement->get_result();
            if ($result)
                return $result->fetch_all(MYSQLI_ASSOC);
            else
                return $statement->insert_id;
        }

        switch ($statement->errno) {
            case MYSQL_DUPLICATE_ERROR:
                throw new DuplicateEntityException();
            case MYSQL_FOREIGN_KEY_ERROR:
                throw new ForeignKeyFailedException();
            default:
                throw new RuntimeException($statement->error);
        }
    }
}