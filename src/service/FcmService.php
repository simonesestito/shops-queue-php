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

define('FCM_TYPE_BOOKING_CANCELLED', 'booking-cancelled');
define('FCM_TYPE_QUEUE_NOTICE', 'queue-notice');
define('FCM_TYPE_ORDER_READY', 'order-ready');
define('FCM_TYPE_ORDER_CANCELLED', 'order-cancelled');

class FcmService {
    private $fcmDao;

    public function __construct(FcmDao $fcmDao) {
        $this->fcmDao = $fcmDao;
    }

    /**
     * Send a DATA payload to every device of a user.
     * If a user's token is invalid, it'll  delete it from the database.
     * @param int $userId
     * @param string $messageType The type of the message (e.g.: booking-cancelled)
     * @param mixed $messageData Data to send
     */
    public function sendPayloadToUser(int $userId, string $messageType, $messageData) {
        $tokens = $this->fcmDao->getTokensByUser($userId);
        foreach ($tokens as $token) {
            $this->sendPayloadOrDeleteToken($token, $messageType, $messageData);
        }
    }

    /**
     * Send a DATA payload to a single device, identified by the given FCM token.
     * If the given token is invalid, it'll delete it from the database.
     * @param string $token FCM token
     * @param string $messageType The type of the message (e.g.: booking-cancelled)
     * @param mixed $messageData Data to send
     */
    private function sendPayloadOrDeleteToken(string $token, string $messageType, $messageData) {
        if (FCM_SERVER_KEY === '') {
            if (SKIP_EMAIL_VERIFICATION === 'true') {
                error_log('Skipping FCM payload sending because no FCM key is defined and debug mode is enabled.');
                return;
            } else {
                throw new RuntimeException('FCM server key not found in env variables');
            }
        }

        $url = 'https://fcm.googleapis.com/fcm/send';
        $headers = ['Authorization: key=' . FCM_SERVER_KEY];
        $body = [
            'to' => $token,
            'data' => [
                'type' => $messageType,
                'data' => $messageData,
            ],
        ];

        $result = httpPost($url, $headers, $body);
        $sendResults = $result['results'];
        foreach ($sendResults as $sendResult) {
            if (@$sendResult['error'] === 'InvalidRegistration')
                $this->fcmDao->deleteToken($token);
        }
    }

    /**
     * Register a FCM token to a specific user.
     * If the token is already used by another user,
     * remove the token from the old user and assign it to the new one.
     * @param int $userId
     * @param string $token FCM token
     */
    public function setOrReplaceToken(int $userId, string $token) {
        $this->fcmDao->deleteToken($token);
        $this->fcmDao->addToken($userId, $token);
    }
}