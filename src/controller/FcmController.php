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

class FcmController extends BaseController {
    private $fcmService;

    public function __construct(FcmService $fcmService) {
        $this->fcmService = $fcmService;
        $this->registerRoute('/users/me/fcm', 'POST', '*', 'addFcmToken');
    }

    /**
     * Add a new FCM token to the current user
     * @param FcmToken $fcmToken
     */
    public function addFcmToken(FcmToken $fcmToken) {
        $userId = AuthService::getAuthContext()['id'];
        $this->fcmService->setOrReplaceToken($userId, $fcmToken->token);
    }
}

onInit(function () {
    registerController(FcmController::class);
});