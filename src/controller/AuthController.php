<?php

require_once __DIR__ . '/BaseController.php';


class AuthController extends BaseController {
    private $userDao;

    public function __construct(UserDao $userDao) {
        $this->userDao = $userDao;
        $this->registerRoute('/signup', 'POST', null, 'signupUser');
    }

    public static function getBaseUrl(): string {
        return '/auth';
    }

    public function signupUser(NewUser $newUser) {
        $this->userDao->insertNewUser($newUser);
        // TODO Login and return
    }
}

onInit(function () {
    registerController(AuthController::class);
});