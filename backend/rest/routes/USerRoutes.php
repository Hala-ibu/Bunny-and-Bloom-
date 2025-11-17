<?php
/**
 * @OA\Post(
 * path="/register",
 * summary="Register a new user",
 * tags={"Users"},
 * @OA\RequestBody(
 * description="User registration data",
 * required=true,
 * @OA\JsonContent(
 * required={"username", "email", "password"},
 * @OA\Property(property="username", type="string", example="johndoe"),
 * @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
 * @OA\Property(property="password", type="string", format="password", example="SecurePass123!")
 * )
 * ),
 * @OA\Response(
 * response=200,
 * description="User registered successfully"
 * ),
 * @OA\Response(
 * response=400,
 * description="Invalid data or user already exists"
 * )
 * )
 */
Flight::route('POST /register', function(){
    $data = Flight::request()->data->getData();
    try {
        $user = Flight::userService()->registerUser($data);
        unset($user['password']); 
        Flight::json($user);
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Post(
 * path="/login",
 * summary="Login an existing user",
 * tags={"Users"},
 * @OA\RequestBody(
 * description="User login credentials",
 * required=true,
 * @OA\JsonContent(
 * required={"email", "password"},
 * @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
 * @OA\Property(property="password", type="string", format="password", example="SecurePass123!")
 * )
 * ),
 * @OA\Response(
 * response=200,
 * description="User logged in successfully"
 * ),
 * @OA\Response(
 * response=401,
 * description="Invalid email or password"
 * )
 * )
 */
Flight::route('POST /login', function(){
    $data = Flight::request()->data->getData();
    
    $email = $data['email'] ?? null;
    $password = $data['password'] ?? null;

    $user = Flight::userService()->login($email, $password);
    
    if ($user) {
        unset($user['password']); 
        Flight::json($user);
    } else {
        Flight::json(['error' => 'Invalid email or password'], 401);
    }
});


?>