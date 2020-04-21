<?php


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
}