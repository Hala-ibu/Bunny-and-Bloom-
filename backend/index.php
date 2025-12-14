<?php
// index.php (in your project root)

// 1. Load Composer Autoloader
require 'vendor/autoload.php';

// 2. Load Global Configuration
require_once 'config.php'; 

// 3. Load FlightPHP Core
use flight\Engine;
$app = new Engine();

// 4. Load and Register Auth Middleware (Path: root/index.php to rest/middleware)
require_once __DIR__ . '/rest/middleware/AuthMiddleware.php'; 
$app->register('auth_middleware', AuthMiddleware::class, [$app]);

// 5. Load and Register All Services
require_once __DIR__ . '/rest/service/UserService.php'; 
require_once __DIR__ . '/rest/service/ProductService.php';
require_once __DIR__ . '/rest/service/OrderService.php'; 
require_once __DIR__ . '/rest/service/ReviewService.php'; 
require_once __DIR__ . '/rest/service/InventoryService.php'; 
require_once __DIR__ . '/rest/service/ContactService.php'; 
// ... register your DAOs and all other necessary files (BaseDao.php, etc.)

$app->register('userService', UserService::class);
$app->register('productService', ProductService::class);
$app->register('orderService', OrderService::class);
$app->register('reviewService', ReviewService::class);
$app->register('inventoryService', InventoryService::class);
$app->register('contactService', ContactService::class);
// ... register all other services

// 6. Global Middleware to Verify JWT (The Security Filter)
// This filter runs before EVERY route
Flight::route('/*', function(){
    $url = Flight::request()->url;
    
    // Whitelist public routes (NO token required)
    if (
        strpos($url, '/auth/login') === 0 || // Login
        strpos($url, '/auth/register') === 0 || // Register
        strpos($url, '/docs') === 0 || // Swagger/OpenAPI docs
        strpos($url, '/contact') === 0 || // <--- ADD THIS LINE
        (Flight::request()->method === 'GET' && strpos($url, '/products') === 0) // Example: Allow all GET product requests
    ) {
        return true; // Skip authentication and proceed to the route handler
    }

    // Authenticate All Other Routes (Requires token)
    try {
        $authHeader = Flight::request()->getHeader('Authorization');
        
        // This verifies the token and sets the user payload: Flight::get('user')
        Flight::auth_middleware()->verifyToken($authHeader);
        
        return true; // Token is valid, proceed to the route handler
        
    } catch (\Exception $e) {
        // Halt execution and return a 401 Unauthorized response
        Flight::halt(401, $e->getMessage()); 
    }
});

// 7. Load Routes
require_once __DIR__ . '/rest/routes/AuthRoutes.php'; // <--- NEW PUBLIC ROUTES

// Load your other route files (e.g., ProductRoutes, UserRoutes, OrderRoutes)
// require_once __DIR__ . '/rest/routes/ProductRoutes.php'; 
// require_once __DIR__ . '/rest/routes/UserRoutes.php';
// ... etc.

$app->start();
?>