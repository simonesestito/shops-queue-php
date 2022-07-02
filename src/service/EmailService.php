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

define('VERIFICATION_CODE_BITS_LENGTH', 256);

class EmailService {
    private $userDao;

    public function __construct(UserDao $userDao) {
        $this->userDao = $userDao;
    }

    public function sendConfirmationToAddress($emailAddress) {
        if (SKIP_EMAIL_VERIFICATION === 'true'
            && SENDGRID_API_KEY === ''
            && SENDGRID_TEMPLATE_ID === ''
            && SENDGRID_FROM_EMAIL === '') {
            // Validate user's email directly.
            $this->userDao->validateEmailWithoutCode($emailAddress);
            return;
        }

        if (SENDGRID_API_KEY === '') {
            throw new RuntimeException('SendGrid API KEY not found in env variables');
        }

        if (SENDGRID_TEMPLATE_ID === '') {
            throw new RuntimeException('SendGrid template ID not found in env variables');
        }

        if (SENDGRID_FROM_EMAIL === '') {
            throw new RuntimeException('SendGrid sender email address not found in env variables');
        }

        $verificationCode = bin2hex(random_bytes(VERIFICATION_CODE_BITS_LENGTH / 8));
        $this->userDao->addVerificationCode($emailAddress, $verificationCode);

        $user = $this->userDao->getUserByEmail($emailAddress);
        
        $url = 'https://api.sendgrid.com/v3/mail/send';
        $headers = [ 'Authorization: Bearer '.SENDGRID_API_KEY ];
        $body = [
            'from' => [
                'name' => 'Shops Queue (no-reply)',
                'email' => SENDGRID_FROM_EMAIL,
            ],
            'personalizations' => [
                [
                    'to' => [
                        [ 'email' => $emailAddress ],
                    ],
                    'dynamic_template_data' => [
                        'name' => $user['name'],
                        'email' => $emailAddress,
                        'code' => $verificationCode,
                    ],
                ]
            ],
            'template_id' => SENDGRID_TEMPLATE_ID,
        ];
        httpPost($url, $headers, $body);
    }

    public function validateEmailAddress($emailAddress, $verificationCode) {
        $this->userDao->validateEmailByCode($emailAddress, $verificationCode);
    }
}