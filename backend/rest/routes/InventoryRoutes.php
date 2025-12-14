<?php

/**
 * @OA\Get(
 * path="/admin/inventory",
 * summary="Get all inventory stock (Admin)",
 * tags={"Admin - Inventory"},
 * @OA\Response(response=200, description="List of all inventory items with product details")
 * )
 */
Flight::route('GET /admin/inventory', function(){
    Flight::auth_middleware()->authorizeRoles(['admin']);    
    Flight::json(Flight::inventoryService()->getAllStockDetails());
});

/**
 * @OA\Put(
 * path="/admin/inventory/@product_id",
 * summary="Adjust stock for a product (Admin)",
 * tags={"Admin - Inventory"},
 * @OA\Parameter(name="product_id", in="path", required=true, @OA\Schema(type="integer")),
 * @OA\RequestBody(
 * description="Stock adjustment data",
 * @OA\JsonContent(
 * required={"quantity_change"},
 * @OA\Property(property="quantity_change", type="integer", example=10)
 * )
 * ),
 * @OA\Response(response=200, description="Stock updated"),
 * @OA\Response(response=400, description="Update failed")
 * )
 */
Flight::route('PUT /admin/inventory/@product_id', function($product_id){
    Flight::auth_middleware()->authorizeRoles(['admin']);    
    $data = Flight::request()->data->getData();
    try {
        $newStock = Flight::inventoryService()->adjustStock($product_id, $data['quantity_change']);
        Flight::json(['message' => 'Stock updated', 'new_stock' => $newStock]);
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});
?>