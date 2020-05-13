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

class SessionController extends BaseController {
    private $sessionDao;

    public function __construct(SessionDao $sessionDao) {
        $this->sessionDao = $sessionDao;
        $this->registerRoute('/users/me/sessions', 'GET', '*', 'getUserSessions');
        $this->registerRoute('/sessions/:id', 'DELETE', '*', 'deleteSession');
    }

    /**
     * Get sessions for the current user
     * @return Session[]
     */
    public function getUserSessions() {
        $userId = AuthService::getAuthContext()['id'];
        $currentSession = AuthService::getAuthContext()['sessionId'];
        $records = $this->sessionDao->getSessionsByUserId($userId);
        return array_map(function ($record) use ($currentSession) {
            $dto = new Session($record);
            $dto->currentSession = $dto->id === $currentSession;
            return $dto;
        }, $records);
    }

    /**
     * Revoke a session
     * @param int $sessionId
     */
    public function deleteSession(int $sessionId) {
        $userId = AuthService::getAuthContext()['id'];
        $this->sessionDao->revokeUserSession($userId, $sessionId);
    }
}

onInit(function () {
    registerController(SessionController::class);
});