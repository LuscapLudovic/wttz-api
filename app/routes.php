<?php

use Symfony\Component\HttpFoundation\Request;
use SilexApi\User;

$app->before(function (Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});

$app->get('/api/user', function () use ($app) {
    $users = $app['dao.user']->findAll();
    $responseData = array();
    foreach ($users as $user){
        $responseData[] = array(
            'id' => $user->getId(),
            'username' => $user->getUsername()
        );
    }

    return $app->json($responseData);
})->bind('api_user');

$app->get('/api/user/{id}', function ($id, Request $request) use ($app) {
    $user = $app['dao.user']->findById($id);
    if(!isset($user)){
        $app->abort(404, "L'utilisateur n'existe pas");
    }

    $responseData = array(
        'id' => $user->getId(),
        'username' => $user->getUsername()
    );
    return $app->json($responseData);
})->bind('api_user');

$app->post('/api/connexion', function(Request $request) use ($app) {
    $user = $app['dao.user']->connexion($request->request->get('username'), $request->request->get('password'));
})->bind('api_user');
