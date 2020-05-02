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
     * @param string $query Search by name
     * @return array Associative array. Key 'count' has the total rows count, 'data' has the actual result
     */
    public function getUsers(int $offset, int $limit, string $query = ''): array {
        $sql = "SELECT SQL_CALC_FOUND_ROWS *
                FROM UserWithRole
                WHERE (name + ' ' + surname) LIKE ?
                    OR (surname + ' ' + name) LIKE ?
                LIMIT ?, ?";
        $data = $this->query($sql, ["%$query%", "%$query%", $offset, $limit]);
        $count = $this->query("SELECT FOUND_ROWS() AS c")[0]['c'];

        return [
            'data' => $data,
            'count' => $count,
        ];
    }

    /**
     * List the owners of a specific shop
     * @param int $shopId
     * @return array Array of the results
     */
    public function getOwnersOf(int $shopId) {
        return $this->query("SELECT * FROM UserWithRole WHERE shopId = ?", [$shopId]);
    }

    /**
     * Add an owner to a specific shop
     * @param int $userId
     * @param int $shopId
     */
    public function addOwnerToShop(int $userId, int $shopId) {
        $this->query("UPDATE User SET shopId = ? WHERE id = ?", [$shopId, $userId]);
    }

    /**
     * Remove an owner from a specific shop
     * @param int $userId
     * @param int $shopId
     */
    public function deleteOwnerFromShop(int $userId, int $shopId) {
        $this->query("UPDATE User SET shopId = null WHERE shopId = ? AND id = ?", [$shopId, $userId]);
    }
}