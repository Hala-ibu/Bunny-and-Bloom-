<?php
/**
 * @OA\Post(
 * path="/orders",
 * summary="Place a new order",
 * tags={"Orders"},
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
 * @OA\Response(response=400, description="Order failed (e.g., out of stock)")
 * )
 */
Flight::route('POST /orders', function(){
    // TODO: Add security check (user can only order for themselves)
    $data = Flight::request()->data->getData();
    try {
        $orderId = Flight::orderService()->placeOrder($data);
        Flight::json(['message' => 'Order placed successfully', 'order_id' => $orderId]);
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Get(
 * path="/orders/user/@user_id",
 * summary="Get all orders for a specific user",
 * tags={"Orders"},
 * @OA\Parameter(name="user_id", in="path", required=true, @OA\Schema(type="integer")),
 * @OA\Response(response=200, description="List of user's orders")
 * )
 */
Flight::route('GET /orders/user/@user_id', function($user_id){
    // TODO: Add security check (user can only see their own orders)
    Flight::json(Flight::orderService()->getByUserId($user_id));
});

/**
 * @OA\Post(
 * path="/orders/@id/cancel",
 * summary="Cancel an order",
 * tags={"Orders"},
 * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 * @OA\Response(response=200, description="Order cancelled"),
 * @OA\Response(response=400, description="Cancellation failed")
 * )
 */
Flight::route('POST /orders/@id/cancel', function($id){
    // TODO: Add security check (user can only cancel their own order)
    try {
        Flight::orderService()->cancelOrder($id);
        Flight::json(['message' => 'Order cancelled successfully']);
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});


/**
 * @OA\Get(
 * path="/admin/orders",
 * summary="Get all orders (Admin)",
 * tags={"Admin - Orders"},
 * @OA\Response(response=200, description="List of all orders")
 * )
 */
Flight::route('GET /admin/orders', function(){
    // TODO: Add security check for admin role
    Flight::json(Flight::orderService()->getAllOrders());
});

/**
 * @OA\Get(
 * path="/admin/orders/@id",
 * summary="Get details for a specific order (Admin)",
 * tags={"Admin - Orders"},
 * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 * @OA\Response(response=200, description="Order details"),
 * @OA\Response(response=404, description="Order not found")
 * )
 */
Flight::route('GET /admin/orders/@id', function($id){
    // TODO: Add security check for admin role
    $order = Flight::orderService()->getOrderDetails($id); // Assumes you create this service method
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
 * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 * @OA\RequestBody(
 * description="New status",
 * @OA\JsonContent(
 * required={"status"},
 * @OA\Property(property="status", type="string", example="Completed")
 * )
 * ),
 * @OA\Response(response=200, description="Status updated")
 * )
 */
Flight::route('PUT /admin/orders/@id/status', function($id){
    // TODO: Add security check for admin role
    $data = Flight::request()->data->getData();
    try {
        Flight::orderService()->updateStatus($id, $data['status']);
        Flight::json(['message' => 'Order status updated']);
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});
?>