<?php
/**
 * @OA\Post(
 * path="/orders",
 * summary="Place a new order",
 * tags={"Orders"},
 * security={{"ApiKeyAuth": {}}},
 * @OA\RequestBody(
 * description="Order data",
 * @OA\JsonContent(
 * required={"user_id", "items"},
 * @OA\Property(property="user_id", type="integer", example=1),
 * @OA\Property(
 * property="items",
 * type="array",
 * @OA\Items(
 * type="object",
 * required={"product_id", "quantity"},
 * @OA\Property(property="product_id", type="integer", example=1),
 * @OA\Property(property="quantity", type="integer", example=2)
 * )
 * )
 * )
 * ),
 * @OA\Response(response=200, description="Order placed successfully"),
 * @OA\Response(response=400, description="Order failed (e.g., out of stock)"),
 * @OA\Response(response=401, description="Unauthorized")
 * )
 */
Flight::route('POST /orders', function(){
    Flight::auth_middleware()->authorizeRoles([Roles::USER, Roles::ADMIN]); 
    $data = Flight::request()->data->getData();
    $user = Flight::get('user');
    
    $data['user_id'] = $user['id']; 

    try {
        $orderId = Flight::orderService()->placeOrder($data);
        Flight::json(['message' => 'Order placed successfully', 'order_id' => $orderId]);
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 400); 
    }
});

/**
 * @OA\Get(
 * path="/orders",
 * summary="Get a list of the authenticated user's orders",
 * tags={"Orders"},
 * security={{"ApiKeyAuth": {}}},
 * @OA\Response(response=200, description="List of user's orders"),
 * @OA\Response(response=401, description="Unauthorized")
 * )
 */
Flight::route('GET /orders', function(){
    Flight::auth_middleware()->authorizeRoles([Roles::USER, Roles::ADMIN]);
    $user = Flight::get('user');
    $user_id = $user['id'];
    
    Flight::json(Flight::orderService()->getOrdersByUserId($user_id));
});

/**
 * @OA\Get(
 * path="/orders/@id",
 * summary="Get details for a specific order by ID (User-Only)",
 * tags={"Orders"},
 * security={{"ApiKeyAuth": {}}},
 * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 * @OA\Response(response=200, description="Order details"),
 * @OA\Response(response=404, description="Order not found or access forbidden")
 * )
 */
Flight::route('GET /orders/@id', function($id){
    Flight::auth_middleware()->authorizeRoles([Roles::USER, Roles::ADMIN]);
    $user = Flight::get('user');
    
    $order = Flight::orderService()->getOrderByUserAndId($user['id'], $id); 
    
    if ($order) {
        Flight::json($order);
    } else {
        Flight::json(['error' => 'Order not found or access forbidden'], 404);
    }
});


/**
 * @OA\Get(
 * path="/admin/orders",
 * summary="Get a list of all orders (Admin)",
 * tags={"Admin - Orders"},
 * security={{"ApiKeyAuth": {}}},
 * @OA\Response(response=200, description="List of all orders")
 * )
 */
Flight::route('GET /admin/orders', function(){
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN); 
    Flight::json(Flight::orderService()->getAllOrders());
});

/**
 * @OA\Get(
 * path="/admin/orders/@id",
 * summary="Get details for a specific order (Admin)",
 * tags={"Admin - Orders"},
 * security={{"ApiKeyAuth": {}}},
 * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 * @OA\Response(response=200, description="Order details"),
 * @OA\Response(response=404, description="Order not found")
 * )
 */
Flight::route('GET /admin/orders/@id', function($id){
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN); 
    $order = Flight::orderService()->getOrderDetails($id);
    if ($order) {
        Flight::json($order);
    } else {
        Flight::json(['error' => 'Order not found'], 404);
    }
});

/**
 * @OA\Put(
 * path="/admin/orders/@id/status",
 * summary="Update an order's status (Admin)",
 * tags={"Admin - Orders"},
 * security={{"ApiKeyAuth": {}}},
 * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 * @OA\RequestBody(
 * description="New status",
 * @OA\JsonContent(
 * required={"status"},
 * @OA\Property(property="status", type="string", example="Completed")
 * )
 * ),
 * @OA\Response(response=200, description="Status updated"),
 * @OA\Response(response=404, description="Order not found")
 * )
 */
Flight::route('PUT /admin/orders/@id/status', function($id){
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN); 
    $data = Flight::request()->data->getData();
    try {
        Flight::orderService()->updateOrderStatus($id, $data['status']); 
        Flight::json(['message' => 'Order status updated successfully']);
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 404);
    }
});
?>