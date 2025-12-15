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
 * @OA\Response(response=200, description="User registered successfully"),
 * @OA\Response(response=400, description="Invalid data or user already exists")
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
 * @OA\Response(response=200, description="User logged in successfully"),
 * @OA\Response(response=401, description="Invalid email or password")
 * )
 */
Flight::route('POST /login', function(){
    $data = Flight::request()->data->getData();
    
    if (empty($data['email']) || empty($data['password'])) {
        Flight::json(['error' => 'Email and password are required.'], 400);
        return;
    }
    
    $userToken = Flight::userService()->login($data['email'], $data['password']); 
    
    if ($userToken) {
        Flight::json($userToken);
    } else {
        Flight::json(['error' => 'Invalid email or password.'], 401);
    }
});

/**
 * @OA\Get(
 * path="/admin/users",
 * summary="Get all users (Admin)",
 * tags={"Admin - Users"},
 * @OA\Response(response=200, description="List of all users")
 * )
 */
Flight::route('GET /admin/users', function(){
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    Flight::json(Flight::userService()->getAllUsers());
});

/**
 * @OA\Get(
 * path="/users/@id",
 * summary="Get user by ID",
 * tags={"Users"},
 * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 * @OA\Response(response=200, description="User details")
 * )
 */
Flight::route('GET /users/@id', function($id){
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);
    $authenticatedUser = Flight::get('user'); 
    
    if ($authenticatedUser['id'] != $id && strtolower($authenticatedUser['role']) != Roles::ADMIN) {
        Flight::halt(403, "Access forbidden. You can only view your own profile.");
    }
    
    $user = Flight::userService()->getById($id);
    if ($user) {
        unset($user['password']);
        Flight::json($user);
    } else {
        Flight::json(['error' => 'User not found'], 404);
    }
});

/**
 * @OA\Put(
 * path="/users/@id",
 * summary="Update user profile",
 * tags={"Users"},
 * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 * @OA\RequestBody(
 * description="User profile data to update",
 * @OA\JsonContent(
 * @OA\Property(property="username", type="string", example="jane.doe"),
 * @OA\Property(property="email", type="string", format="email", example="jane.doe@example.com")
 * )
 * ),
 * @OA\Response(response=200, description="Profile updated"),
 * @OA\Response(response=403, description="Access forbidden")
 * )
 */
Flight::route('PUT /users/@id', function($id){
    Flight::auth_middleware()->authorizeRoles([Roles::ADMIN, Roles::USER]);
    $authenticatedUser = Flight::get('user'); 

    if ($authenticatedUser['id'] != $id && strtolower($authenticatedUser['role']) != Roles::ADMIN) {
        Flight::halt(403, "Access forbidden. You can only update your own profile.");
    }

    $data = Flight::request()->data->getData();
    try {
        $updatedUser = Flight::userService()->updateProfile($id, $data);
        unset($updatedUser['password']);
        Flight::json($updatedUser);
    } catch (\Exception $e) {
        Flight::halt(400, $e->getMessage());
    }
});

/**
 * @OA\Delete(
 * path="/admin/users/@id",
 * summary="Delete a user (Admin)",
 * tags={"Admin - Users"},
 * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 * @OA\Response(response=200, description="User deleted")
 * )
 */
Flight::route('DELETE /admin/users/@id', function($id){
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    
    try {
        Flight::userService()->delete($id); 
        Flight::json(['message' => 'User deleted successfully']);
    } catch (\Exception $e) {
        Flight::halt(400, $e->getMessage());
    }
});
?>