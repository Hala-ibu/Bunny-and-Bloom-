<?php
Flight::group('/auth', function() {
    
    Flight::route('POST /register', function() {
        $data = Flight::request()->data->getData();
        
        $response = Flight::authService()->register($data);
        if ($response['success']) {
            Flight::json($response['data'], 201);
        } else {
            Flight::halt(400, $response['error']);
        }
    });

    Flight::route('POST /login', function() {
        $data = Flight::request()->data->getData();
        
        $response = Flight::authService()->login($data);

        if ($response['success']) {
            Flight::json($response['data'], 200);
        } else {
            Flight::halt(401, $response['error']);
        }
    });

});