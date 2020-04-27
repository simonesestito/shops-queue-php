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

class UserDao extends Dao {
    /**
     * Insert a new user account
     * @param NewUser $newUser
     * @return int ID of the newly created user
     */
    public function insertNewUser(NewUser $newUser) {
        // Find the ID of the given role
        $role = $newUser->role;
        $roles = $this->query("SELECT * FROM Role WHERE name = ?", [$role]);
        if (empty($roles)) {
            throw new RuntimeException("Unable to find role: $role");
        }
        $roleId = $roles[0]['id'];

        $params = [
            $newUser->name,
            $newUser->surname,
            $newUser->email,
            password_hash($newUser->password, PASSWORD_BCRYPT),
            $roleId,
            $newUser->shopId,
        ];

        $sql = "INSERT INTO User (name, surname, email, password, roleId, shopId) VALUES (?, ?, ?, ?, ?, ?)";
        return $this->query($sql, $params);
    }

    /**
     * Delete a user
     * @param $id int
     */
    public function deleteUser(int $id) {
        $this->query("DELETE FROM User WHERE id = ?", [$id]);
    }

    /**
     * Find a user by his email address
     * @param string $email
     * @return array|null Single associative array
     */
    public function getUserByEmail(string $email) {
        $records = $this->query("SELECT * FROM UserWithRole WHERE email = ?", [$email]);
        return @$records[0];
    }

    /**
     * Find a user by ID
     * @param int $id
     * @return array|null Single associative array
     */
    public function getUserById(int $id) {
        $records = $this->query("SELECT * FROM UserWithRole WHERE id = ?", [$id]);
        return @$records[0];
    }

    /**
     * List users using pagination
     * @param int $offset Number of items to skip
     * @param int $limit Max number of items to return
     * @param string|null $role Search by role
     * @param int|null $shopId Search by shop ID
     * @return array Associative array. Key 'count' has the total rows count, 'data' has the actual result
     */
    public function getUsers(int $offset, int $limit, string $role = null, int $shopId = null): array {
        $params = [];

        $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM UserWithRole WHERE 1";
        if ($role !== null) {
            $sql .= " AND role = ?";
            $params[] = $role;
        }
        if ($shopId !== null) {
            $sql .= ' AND shopId = ?';
            $params[] = $shopId;
        }

        $sql .= " LIMIT ?, ?";
        $params[] = $offset;
        $params[] = $limit;

        $data = $this->query($sql, $params);
        $count = $this->query("SELECT FOUND_ROWS() AS c")[0]['c'];

        return [
            'data' => $data,
            'count' => $count,
        ];
    }
}