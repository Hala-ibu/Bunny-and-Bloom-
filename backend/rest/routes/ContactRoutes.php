<?php
/**
 * @OA\Post(
 * path="/contact",
 * summary="Submit a contact form message",
 * tags={"Contact"},
 * @OA\RequestBody(
 * description="Contact message",
 * @OA\JsonContent(
 * required={"sender_name", "sender_email", "message_content"},
 * @OA\Property(property="sender_name", type="string", example="Jane Doe"),
 * @OA\Property(property="sender_email", type="string", example="jane@example.com"),
 * @OA\Property(property="message_content", type="string", example="Hello!")
 * )
 * ),
 * @OA\Response(response=200, description="Message sent"),
 * @OA\Response(response=400, description="Invalid data")
 * )
 */
Flight::route('POST /contact', function(){
    $data = Flight::request()->data->getData();
    try {
        $message = Flight::contactService()->submitMessage($data);
        Flight::json($message);
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});


/**
 * @OA\Get(
 * path="/admin/contact",
 * summary="Get all contact messages (Admin)",
 * tags={"Admin - Contact"},
 * @OA\Response(response=200, description="List of all messages")
 * )
 */
Flight::route('GET /admin/contact', function(){
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN); 
    Flight::json(Flight::contactService()->getAllMessages());
});
?>