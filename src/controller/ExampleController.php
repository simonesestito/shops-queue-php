<?php

class ExampleController extends BaseController {
    public function __construct() {
        $this->registerRoute('/:name', 'GET', NULL, 'hello');
        $this->registerRoute('', 'GET', NULL, 'err');
    }

    public static function getBaseUrl(): string {
        return '/hello';
    }

    public function hello($name) {
        return 'Hello, '.$name;
    }

    public function err() {
        throw new AppHttpException(418);
    }
}

onInit(function() {
    registerController(ExampleController::class);
});
