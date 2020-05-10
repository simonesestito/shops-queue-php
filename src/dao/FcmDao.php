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

class FcmDao extends Dao {
    /**
     * Add a new FCM Token to the database
     * @param int $userId
     * @param string $token
     * @throws DuplicateEntityException
     */
    public function addToken(int $userId, string $token) {
        $this->query("INSERT INTO FcmToken (token, userId) VALUES (?, ?)", [
            $token,
            $userId
        ]);
    }

    /**
     * Delete a FCM token, if it exists
     * @param string $token FCM token
     */
    public function deleteToken(string $token) {
        $this->query("DELETE FROM FcmToken WHERE token = ?", [$token]);
    }

    /**
     * List all FCM tokens assigned to a user
     * @param int $userId
     * @return string[] Array of tokens, not an array of records
     */
    public function getTokensByUser(int $userId): array {
        $records = $this->query("SELECT token FROM FcmToken WHERE userId = ?", [$userId]);
        return array_map(function ($record) {
            return $record['token'];
        }, $records);
    }
}