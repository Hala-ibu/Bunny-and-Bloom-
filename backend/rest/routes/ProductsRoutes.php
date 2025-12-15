<?php
/**
 * @OA\Get(
 * path="/products",
 * summary="Get all products",
 * tags={"Products"},
 * @OA\Response(response=200, description="List of all products")
 * )
 */
Flight::route('GET /products', function(){
    Flight::json(Flight::productService()->getAllProducts());
});

/**
 * @OA\Get(
 * path="/products/@id",
 * summary="Get a single product by ID",
 * tags={"Products"},
 * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 * @OA\Response(response=200, description="Product details"),
 * @OA\Response(response=404, description="Product not found")
 * )
 */
Flight::route('GET /products/@id', function($id){
    $product = Flight::productService()->getById($id);
    if ($product) {
        Flight::json($product);
    } else {
        Flight::json(['error' => 'Product not found'], 404);
    }
});

/**
 * @OA\Get(
 * path="/products/category/@name",
 * summary="Get products by category name",
 * tags={"Products"},
 * @OA\Parameter(name="name", in="path", required=true, @OA\Schema(type="string")),
 * @OA\Response(response=200, description="List of products in that category")
 * )
 */
Flight::route('GET /products/category/@name', function($name){
    Flight::json(Flight::productService()->getByCategory($name));
});


/**
 * @OA\Post(
 * path="/admin/products",
 * summary="Create a new product (Admin)",
 * tags={"Admin - Products"},
 * @OA\RequestBody(
 * description="Product data",
 * @OA\JsonContent(
 * required={"name", "price", "description"},
 * @OA\Property(property="name", type="string", example="New Coffee"),
 * @OA\Property(property="price", type="number", format="float", example=4.99),
 * @OA\Property(property="description", type="string", example="Drinks")
 * )
 * ),
 * @OA\Response(response=200, description="Product created"),
 * @OA\Response(response=400, description="Invalid data")
 * )
 */
Flight::route('POST /admin/products', function(){
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);    
    $data = Flight::request()->data->getData();
    try {
        $newProduct = Flight::productService()->createProduct($data);
        Flight::json($newProduct);
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Put(
 * path="/admin/products/@id",
 * summary="Update a product (Admin)",
 * tags={"Admin - Products"},
 * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 * @OA\RequestBody(
 * description="Product data to update",
 * @OA\JsonContent(
 * @OA\Property(property="name", type="string"),
 * @OA\Property(property="price", type="number", format="float")
 * )
 * ),
 * @OA\Response(response=200, description="Product updated")
 * )
 */
Flight::route('PUT /admin/products/@id', function($id){
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);    
    $data = Flight::request()->data->getData();
    try {
        Flight::json(Flight::productService()->updateProduct($id, $data));
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Delete(
 * path="/admin/products/@id",
 * summary="Delete a product (Admin)",
 * tags={"Admin - Products"},
 * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 * @OA\Response(response=200, description="Product deleted")
 * )
 */
Flight::route('DELETE /admin/products/@id', function($id){
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);    
    try {
        Flight::productService()->deleteProduct($id);
        Flight::json(['message' => 'Product deleted successfully']);
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 500);
    }
});
?>