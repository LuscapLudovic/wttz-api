<?php

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use SilexApi\User;
use SilexApi\Team;

$app->after(function (Request $request, Response $response) {
    $response->headers->set('Access-Control-Allow-Origin', '*');
    $response->headers->set('Access-Control-Allow-Headers', '*');
});

$app->options("{anything}", function () {
    return new \Symfony\Component\HttpFoundation\JsonResponse(null, 204);
})->assert("anything", ".*");

$app->options("{anything", function () {
    return new \Symfony\Component\HttpFoundation\JsonResponse(null, 201);
})->assert("anything", ".*");


$app->get('/api/users', function () use ($app) {
    $users = $app['dao.user']->findAll();
    $responseData = array();
    foreach ($users as $user){
        $responseData[] = array(
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'team' => $user->getTeam()
        );
    }

    return $app->json($responseData);
})->bind('api_users');

$app->get('/api/teams', function () use ($app) {
    $teams = $app['dao.team']->findAll();
    $responseData = array();
    foreach ($teams as $team){
        $responseData[] = array(
            'id' => $team->getId(),
            'username' => $team->getLibelle()
        );
    }
    return $app->json($responseData);
})->bind('api_teams');

$app->get('/api/user/{id}', function ($id, Request $request) use ($app) {
    $user = $app['dao.user']->findById($id);
    if(!isset($user)){
        $app->abort(404, "L'utilisateur n'existe pas");
    }

    $responseData = array(
        'id' => $user->getId(),
        'username' => $user->getUsername(),
        'team' => $user->getTeam()
    );
    return $app->json($responseData);
})->bind('api_user');

$app->get('/api/team/{id}', function($id, Request $request) use ($app) {
    $team = $app['dao.team']->findById($id);
    if(!isset($team)){
        $app->abort(404, "Il n'y a pas de team avec ce nom");
    }

    $responseData = array(
        'id' => $team->getId(),
        'libelle' => $team->getLibelle()
    );
    return $app->json($responseData);
})->bind('api_team');

$app->post('/api/connexion', function(Request $request) use ($app) {
    $user = $app['dao.user']->connexion($request->request->get('username'), $request->request->get('password'));
    return $user;
})->bind('api_connexion');

$app->post('/api/createUser', function (Request $request) use ($app) {
    if (!$request->request->has('username')) {
        return $app->json('Missing parameter: username', 400);
    }
    if (!$request->request->has('password')) {
        return $app->json('Missing parameter: password', 400);
    }

    $user = new User();
    $user->setUsername($request->request->get('username'));
    $user->setPassword($request->request->get('password'));
    $user->setTeam($request->request->get('team_id'));
    $app['dao.user']->save($user);

    $responseData = array(
        //'id' => $user->getId(),
        'username' => $user->getUsername(),
        'password' => $user->getPassword()
    );

    return $app->json($responseData, 201);
})->bind('api_user_add');

$app->post('/api/createTeam', function(Request $request) use ($app) {
    if(!$request->request->has('libelle')){
        return $app->json('Missing parameter: libelle', 400);
    }

    $team = new Team();
    $team->setLibelle($request->request->get('libelle'));
    $app['dao.team']->save($team);
    $responseData = array(
        'libelle' => $team->getLibelle()
    );

    return $app->json($responseData, 201);
})->bind('api_team_add');

