<?php

use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;
use Symfony\Component\HttpFoundation\Request;

ErrorHandler::register();
ExceptionHandler::register();

$app->register(new Silex\Provider\DoctrineServiceProvider());

$app['dao.user'] = $app->share(function ($app) {
    return new SilexApi\UserDao($app['db']);
});

$app['dao.team'] = $app->share(function ($app) {
    return new SilexApi\TeamDao($app['db']);
});

$app['dao.message'] = $app->share(function ($app) {
    return new SilexApi\MessageDao($app['db']);
});

$app['dao.cryptage'] = $app->share(function ($app) {
    return new SilexApi\CryptageDao($app['db']);
});

// Register JSON data decoder for JSON requests
$app->before(function (Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});
