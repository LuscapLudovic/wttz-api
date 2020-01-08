<?php

use SilexApi\TeamDao;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use SilexApi\User;
use SilexApi\Team;
use SilexApi\Message;

$app->after(function (Request $request, Response $response) {
    $response->headers->set('Access-Control-Allow-Origin', '*');
    $response->headers->set('Access-Control-Allow-Headers', '*');
});

$app->options("{anything}", function () {
    return new \Symfony\Component\HttpFoundation\JsonResponse(null, 204);
})->assert("anything", ".*");

$app->options("{anything}", function () {
    return new \Symfony\Component\HttpFoundation\JsonResponse(null, 201);
})->assert("anything", ".*");


$app->get('/api/user', function () use ($app) {
    $users = $app['dao.user']->findAll();
    $responseData = array();
    /** @var User $user */
    foreach ($users as $user){
        $responseData[] = array(
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'team' => array(
                'id' => $user->getTeam()->getId(),
                'libelle' => $user->getTeam()->getLibelle()
            )
        );
    }

    return $app->json($responseData);
})->bind('api_users');

$app->get('/api/team', function () use ($app) {
    $teams = $app['dao.team']->findAll();
    $responseData = array();
    /** @var Team $team */
    foreach ($teams as $team){
        $responseData[] = array(
            'id' => $team->getId(),
            'libelle' => $team->getLibelle()
        );
    }
    return $app->json($responseData);
})->bind('api_teams');

$app->get('/api/message', function() use ($app) {
    $messages = $app['dao.message']->findAll();
    $responseData = array();
    /** @var Message $message */
    foreach ($messages as $message){
        $responseData[] = array(
            'text' => $message->getText(),
            'posted_at' => $message->getPostedAt(),
            'user_id' => array(
                'id' => $message->getUser()->getId(),
                'username' => $message->getUser()->getUsername()
            ),
            'team_id' => array(
                'id' => $message->getTeam()->getId(),
                'libelle' => $message->getTeam()->getLibelle()
            )
        );
    }
    return $app->json($responseData);
})->bind('api_messages');

$app->get('/api/user/{id}', function ($id, Request $request) use ($app) {
    /** @var User $user */
    $user = $app['dao.user']->findById($id);
    if(!isset($user)){
        $app->abort(404, "L'utilisateur n'existe pas");
    }

    $responseData = array(
        'id' => $user->getId(),
        'username' => $user->getUsername(),
        'team' => array(
            'id' => $user->getTeam()->getId(),
            'libelle' => $user->getTeam()->getLibelle()
        )
    );
    return $app->json($responseData);
})->bind('api_user');

$app->get('/api/team/{id}', function($id, Request $request) use ($app) {
    /** @var Team $team */
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

$app->get('api/message/{id}', function($id, Request $request) use ($app) {
    /** @var Message $message */
    $message = $app['dao.message']->findById($id);
    if(!isset($message)){
        $app->abort(404, "Il n'y pas de message avec ce nom");
    }

    $responseData = array(
        'id' => $message->getId(),
        'text' => $message->getText(),
        'posted_at' => $message->getPostedAt(),
        'user_id' => array(
            'id' => $message->getUser()->getId(),
            'username' => $message->getUser()->getUsername()
        ),
        'team_id' => array(
            'id' => $message->getTeam()->getId(),
            'libelle' => $message->getTeam()->getLibelle()
        )
    );
    return $app->json($responseData);
})->bind('api_message');

$app->post('/api/connexion', function(Request $request) use ($app) {
    /** @var User $user */
    $user = $app['dao.user']->connexion($request->request->get('username'), $request->request->get('password'));
    if($user){
        $session = array(
            'username' => $user->getUsername(),
            'team_id' => $user->getTeam()
        );
    }
    return $app->json($session);
})->bind('api_connexion');

$app->post('/api/user/create', function (Request $request) use ($app) {
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

$app->post('/api/team/create', function(Request $request) use ($app) {
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

$app->post('api/message/create', function(Request $request) use ($app) {
    /*if(!$request->request->has('text')){
        return $app->json('Missing parameter: text', 400);
    }
    if(!$request->request->has('user_id')){
        return $app->json('Missing parameter: user_id', 400);
    }
    if(!$request->request->has('text')){
        return $app->json('Missing parameter: team_id', 400);
    }*/

    $message = new Message();
    $message->setText($request->request->get('text'));
    //$message->setPostedAt($request->request->get('posted_at'));
    $message->setTeam($request->request->get('team_id'));
    $message->setUser($request->request->get('user_id'));
    $app['dao.message']->save($message);
    $responseData = array(
        'text' => $message->getText(),
        'user_id' => $message->getUser(),
        'team_id' => $message->getTeam()
    );

    return $app->json($responseData, 201);
})->bind('api_message_add');

