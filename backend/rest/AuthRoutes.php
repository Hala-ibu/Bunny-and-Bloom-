<?php

use flight\Engine;

Flight::group('/auth', function(Engine $router) {
    
    $router->post('/register', function() {
        $data = Flight::request()->data->getData();
        try {
            $result = Flight::userService()->registerUser($data);
            Flight::json($result, 201);
        } catch (\Exception $e) {
            Flight::halt(400, json_encode(['message' => $e->getMessage()]));
        }
    });

    $router->post('/login', function() {
        $data = Flight::request()->data->getData();
        
        if (empty($data['email']) || empty($data['password'])) {
             Flight::halt(400, json_encode(['message' => 'Email and password are required.']));
        }
        
        $userToken = Flight::userService()->login($data['email'], $data['password']);
        
        if ($userToken) {
            Flight::json($userToken);
        } else {
            Flight::halt(401, json_encode(['message' => 'Invalid credentials.']));
        }
    });
});