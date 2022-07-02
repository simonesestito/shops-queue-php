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
     * Insert a new simple user in the database.
     * It will be deactivated, and it'll have a verification code associated.
     * @param NewSimpleUser $newUser
     * @return int ID of the newly created user
     */
    public function insertNewSimpleUser(NewSimpleUser $newUser): int {
        $params = [
            $newUser->name,
            $newUser->surname,
            $newUser->email,
            password_hash($newUser->password, PASSWORD_BCRYPT),
        ];
        return $this->query("INSERT INTO User (name, surname, email, password) VALUES (?, ?, ?, ?)", $params);
    }

    /**
     * Insert a new user account
     * @param NewUser $newUser
     * @return int ID of the newly created user
     */
    public function insertNewUser(NewUser $newUser) {
        $params = [
            $newUser->name,
            $newUser->surname,
            $newUser->email,
            password_hash($newUser->password, PASSWORD_BCRYPT),
            $this->getRoleId($newUser->role),
            $newUser->shopId,
            $newUser->active,
        ];

        $sql = "INSERT INTO User (name, surname, email, password, roleId, shopId, active) VALUES (?, ?, ?, ?, ?, ?, ?)";
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
     * @return array|null Single associative array of UserDetails records
     */
    public function getUserByEmail(string $email) {
        $records = $this->query("SELECT * FROM UserDetails WHERE email = ?", [$email]);
        return @$records[0];
    }

    /**
     * Find a user by ID
     * @param int $id
     * @return array|null Single associative array of UserDetails records
     */
    public function getUserById(int $id) {
        $records = $this->query("SELECT * FROM UserDetails WHERE id = ?", [$id]);
        return @$records[0];
    }

    /**
     * List users using pagination
     * @param int $offset Number of items to skip
     * @param int $limit Max number of items to return
     * @param string $query Search by name
     * @param int|null $shopId Search by shop ID
     * @return array Associative array of UserDetails records. Key 'count' has the total rows count, 'data' has the actual result
     */
    public function getUsers(int $offset, int $limit, string $query, $shopId): array {
        $params = ["%$query%", "%$query%"];
        $sql = "SELECT SQL_CALC_FOUND_ROWS *
                FROM UserDetails
                WHERE ( CONCAT_WS(' ', name, surname) LIKE ?
                    OR CONCAT_WS(' ', surname, name) LIKE ? )";

        if ($shopId !== null) {
            $sql .= ' AND shopId = ?';
            $params[] = $shopId;
        }

        $sql .= ' ORDER BY surname, name, id LIMIT ?, ?';
        $params[] = $offset;
        $params[] = $limit;

        $data = $this->query($sql, $params);
        $count = $this->query("SELECT FOUND_ROWS() AS c")[0]['c'];

        return [
            'data' => $data,
            'count' => $count,
        ];
    }

    /**
     * Update a user
     * @param int $id User ID
     * @param UserUpdate $update
     * @noinspection SqlWithoutWhere
     */
    public function updateUser(int $id, UserUpdate $update) {
        $roleId = $this->getRoleId($update->role);

        $sql = "UPDATE User
        SET name = ?,
        surname = ?,
        email = ?,
        roleId = ?,
        shopId = ?,
        active = ?";
        $params = [$update->name, $update->surname, $update->email, $roleId, $update->shopId, $update->active];

        // Handle password edit
        if ($update->password !== null) {
            $update->password = password_hash($update->password, PASSWORD_BCRYPT);
            $sql .= ", password = ?";
            $params[] = $update->password;
        }

        $sql .= " WHERE id = ?";
        $params[] = $id;

        $this->query($sql, $params);
    }

    /**
     * Update a simple user
     * @param int $id User ID
     * @param SimpleUserUpdate $update
     * @noinspection SqlWithoutWhere
     */
    public function updateSimpleUser(int $id, SimpleUserUpdate $update) {
        $sql = "UPDATE User
        SET name = ?,
        surname = ?,
        email = ?";
        $params = [$update->name, $update->surname, $update->email];

        // Handle password edit
        if ($update->password !== null) {
            $update->password = password_hash($update->password, PASSWORD_BCRYPT);
            $sql .= ", password = ?";
            $params[] = $update->password;
        }

        $sql .= " WHERE id = ?";
        $params[] = $id;

        $this->query($sql, $params);
    }

    /**
     * Add a verification code to a user
     * @param string $userEmail
     * @param string $verificationCode
     */
    public function addVerificationCode($userEmail, $verificationCode) {
        $this->query("UPDATE User SET verificationCode = ?, active = false WHERE email = ?", [$verificationCode, $userEmail]);
    }

    /**
     * Validate a user's email
     * @param string $userEmail
     * @param string $verificationCode
     */
    public function validateEmailByCode($userEmail, $verificationCode) {
        $this->query("UPDATE User SET verificationCode = NULL, active = true WHERE verificationCode = ? AND email = ?", [
            $verificationCode,
            $userEmail
        ]);
    }

    /**
     * Validate a user's email without checking the verification code
     * @param string $userEmail
     */
    public function validateEmailWithoutCode($userEmail) {
        $this->query("UPDATE User SET verificationCode = NULL, active = true WHERE email = ?", [
            $userEmail
        ]);
    }

    /**
     * Find the ID of the given role
     * @param string $role
     * @return int
     */
    private function getRoleId(string $role): int {
        $roles = $this->query("SELECT * FROM Role WHERE name = ?", [$role]);
        if (empty($roles)) {
            throw new RuntimeException("Unable to find role: $role");
        }
        return $roles[0]['id'];
    }
}