<?php

define('DATETIME_FORMAT', 'Y-m-d H:i:s');

class SessionDao extends Dao {
    public function createNewSession(array $sessionData): array {
        $sql = "INSERT INTO Session (userId, accessToken, accessTokenExpiration, refreshToken, refreshTokenExpiration)
                VALUES (?, ?, ?, ?, ?)";

        $id = $this->query($sql, [
            $sessionData['userId'],
            $sessionData['accessToken'],
            $sessionData['accessTokenExpiration'],
            $sessionData['refreshToken'],
            $sessionData['refreshTokenExpiration']
        ]);

        $sessionData['id'] = $id;
        return $sessionData;
    }

    /**
     * Get details about the user associated with the given access token, if any.
     * @param string $accessToken
     * @return mixed Session record with user and role info
     */
    public function getSessionByAccessToken(string $accessToken) {
        $sql = "SELECT User.*,
                Role.name AS role
                FROM Session
                JOIN User on Session.userId = User.id
                JOIN Role on User.roleId = Role.id
                WHERE accessToken = ? AND accessTokenExpiration > CURRENT_DATE()";
        $records = $this->query($sql, [$accessToken]);
        return @$records[0];
    }
}