<?php

class ExampleController extends BaseController {
    public function __construct() {
        $this->registerRoute('/:name', 'GET', NULL, 'hello');
        $this->registerRoute('', 'GET', NULL, 'err');
        $this->registerRoute('', 'POST', null, 'echoBody');
    }

    public static function getBaseUrl(): string {
        return '/hello';
    }

    public function hello($name) {
        return 'Hello, '.$name;
    }

    public function echoBody(NewUser $body) {
        return $body;
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    public function err() {
        throw new AppHttpException(418);
    }
}

onInit(function() {
    registerController(ExampleController::class);
});
