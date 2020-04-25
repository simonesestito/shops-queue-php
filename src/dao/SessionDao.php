<?php

define('DATETIME_FORMAT', 'Y-m-d H:i:s');

class SessionDao extends Dao {
    public function createNewSession(array $sessionData): array {
        $sql = "INSERT INTO Session (userId, accessToken)
                VALUES (?, ?)";

        $id = $this->query($sql, [
            $sessionData['userId'],
            $sessionData['accessToken']
        ]);

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