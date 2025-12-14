<?php
/**
 * @OA\Get(
 * path="/reviews/product/@product_id",
 * summary="Get all reviews for a product",
 * tags={"Reviews"},
 * @OA\Parameter(name="product_id", in="path", required=true, @OA\Schema(type="integer")),
 * @OA\Response(response=200, description="List of reviews")
 * )
 */
Flight::route('GET /reviews/product/@product_id', function($product_id){
    Flight::json(Flight::reviewService()->getReviewsByProduct($product_id));
});

/**
 * @OA\Post(
 * path="/reviews",
 * summary="Submit a new review",
 * tags={"Reviews"},
 * @OA\RequestBody(
 * description="Review data",
 * @OA\JsonContent(
 * required={"user_id", "product_id", "rating", "comment"},
 * @OA\Property(property="user_id", type="integer", example=1),
 * @OA\Property(property="product_id", type="integer", example=1),
 * @OA\Property(property="rating", type="integer", example=5),
 * @OA\Property(property="comment", type="string", example="Great coffee!")
 * )
 * ),
 * @OA\Response(response=200, description="Review submitted"),
 * @OA\Response(response=400, description="Submission failed")
 * )
 */
Flight::route('POST /reviews', function(){
    Flight::auth_middleware()->authorizeRoles(['user', 'admin']); // **FIX:** Allow regular 'user' role
    $data = Flight::request()->data->getData();
    $user = Flight::get('user');

    // **SECURITY FIX:** Force the user_id to be the authenticated user's ID
    $data['user_id'] = $user['id']; 

    try {
        $review = Flight::reviewService()->submitReview($data);
        Flight::json($review);
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});


/**
 * @OA\Get(
 * path="/admin/reviews",
 * summary="Get all reviews (Admin)",
 * tags={"Admin - Reviews"},
 * @OA\Response(response=200, description="List of all reviews")
 * )
 */
Flight::route('GET /admin/reviews', function(){
    Flight::auth_middleware()->authorizeRoles(['admin']);    
    Flight::json(Flight::reviewService()->getAllReviews());
});

/**
 * @OA\Delete(
 * path="/admin/reviews/@id",
 * summary="Delete a review (Admin)",
 * tags={"Admin - Reviews"},
 * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 * @OA\Response(response=200, description="Review deleted")
 * )
 */
Flight::route('DELETE /admin/reviews/@id', function($id){
    Flight::auth_middleware()->authorizeRoles(['admin']);    
    try {
        Flight::reviewService()->deleteReview($id);
        Flight::json(['message' => 'Review deleted successfully']);
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 500);
    }
});
?>