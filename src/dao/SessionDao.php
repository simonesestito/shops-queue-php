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

define('DATETIME_FORMAT', 'Y-m-d H:i:s');

class SessionDao extends Dao {
    /**
     * Create a new session
     * @param array $sessionData
     * @return int ID of the new session
     */
    public function createNewSession(array $sessionData) {
        $sql = "INSERT INTO Session (userId, accessToken)
                VALUES (?, ?)";

        return $this->query($sql, [
            $sessionData['userId'],
            $sessionData['accessToken'],
        ]);
    }

    /**
     * Get all user's sessions
     * @param int $userId
     * @return array Found sessions with details
     */
    public function getSessionsByUserId(int $userId) {
        return $this->query("SELECT * FROM SessionDetail WHERE id = ? AND sessionActive = TRUE ORDER BY lastUsageDate DESC", [$userId]);
    }

    public function revokeUserSession(int $userId, int $sessionId) {
        $this->query("UPDATE Session SET active = FALSE WHERE userId = ? AND id = ?", [$userId, $sessionId]);
    }

    /**
     * Get details about the user associated with the given access token, if any.
     * @param string $accessToken
     * @return mixed Session record with user and role info
     */
    public function getSessionByAccessToken(string $accessToken) {
        $records = $this->query("SELECT * FROM SessionDetail WHERE accessToken = ? AND sessionActive = TRUE", [$accessToken]);
        return @$records[0];
    }

    public function revokeSessionByAccessToken(string $accessToken) {
        $this->query("UPDATE Session SET active = FALSE WHERE accessToken = ?", [$accessToken]);
    }

    public function updateLastUsageDate(int $sessionId) {
        $this->query("UPDATE Session SET lastUsageDate = NOW() WHERE id = ?", [$sessionId]);
    }
}
