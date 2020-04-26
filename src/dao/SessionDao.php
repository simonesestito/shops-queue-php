<?php

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
            $sessionData['accessToken']
        ]);
    }

    /**
     * Get a session
     * @param int $id
     * @return array|null Found session with details
     */
    public function getSessionById(int $id) {
        $records = $this->query("SELECT * FROM SessionDetail WHERE sessionId = ?", [$id]);
        return @$records[0];
    }

    /**
     * Get details about the user associated with the given access token, if any.
     * @param string $accessToken
     * @return mixed Session record with user and role info
     */
    public function getSessionByAccessToken(string $accessToken) {
        $records = $this->query("SELECT * FROM SessionDetail WHERE accessToken = ?", [$accessToken]);
        return @$records[0];
    }

    public function removeSessionByAccessToken(string $accessToken) {
        $this->query("DELETE FROM Session WHERE accessToken = ?", [$accessToken]);
    }
}